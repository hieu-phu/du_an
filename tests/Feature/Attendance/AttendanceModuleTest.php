<?php

namespace Tests\Feature\Attendance;

use App\Jobs\SendApprovalDecisionEmailJob;
use App\Models\AttendanceApproval;
use App\Models\AttendanceAdjustment;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRequest;
use App\Models\AttendanceMonthLock;
use App\Models\ApprovalRequest;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\EmployeeWorkShiftAssignment;
use App\Models\Position;
use App\Models\PayrollPeriod;
use Carbon\Carbon;
use App\Models\Holiday;
use App\Models\LeaveType;
use App\Models\Notification;
use App\Models\OvertimeRequest;
use App\Models\Project;
use App\Models\SalaryAdjustment;
use App\Models\SalarySnapshot;
use App\Models\User;
use App\Models\WorkShift;
use App\Support\PositionCapability as Capability;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AttendanceModuleTest extends TestCase
{
    private array $testingDatabaseConfig = [];
    private ?string $temporaryDatabaseName = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareIsolatedTestingDatabase();
        $this->artisan('migrate');
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 9, 0, 0, 'Asia/Ho_Chi_Minh'));

    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
        $this->dropTemporaryTestingDatabase();
    }

    private function prepareIsolatedTestingDatabase(): void
    {
        $connectionName = config('database.default');
        $config = config("database.connections.{$connectionName}");

        if (($config['driver'] ?? null) !== 'mysql') {
            return;
        }

        DB::purge($connectionName);

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';
        $collation = $config['collation'] ?? 'utf8mb4_unicode_ci';
        $database = sprintf(
            '%s_attendance_%s',
            (string) ($config['database'] ?? 'laravel_test'),
            substr(sha1((string) microtime(true).$this->name()), 0, 10)
        );

        $this->testingDatabaseConfig = [
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password,
            'charset' => $charset,
        ];
        $this->temporaryDatabaseName = $database;

        $pdo = new \PDO(
            "mysql:host={$host};port={$port};charset={$charset}",
            $username,
            $password,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $quotedDatabase = str_replace('`', '``', $database);
        $pdo->exec("CREATE DATABASE `{$quotedDatabase}` CHARACTER SET {$charset} COLLATE {$collation}");

        config(["database.connections.{$connectionName}.database" => $database]);
        putenv("DB_DATABASE={$database}");

        DB::purge($connectionName);
    }

    private function dropTemporaryTestingDatabase(): void
    {
        if (!$this->temporaryDatabaseName || $this->testingDatabaseConfig === []) {
            return;
        }

        $host = $this->testingDatabaseConfig['host'];
        $port = $this->testingDatabaseConfig['port'];
        $username = $this->testingDatabaseConfig['username'];
        $password = $this->testingDatabaseConfig['password'];
        $charset = $this->testingDatabaseConfig['charset'];

        $pdo = new \PDO(
            "mysql:host={$host};port={$port};charset={$charset}",
            $username,
            $password,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $quotedDatabase = str_replace('`', '``', $this->temporaryDatabaseName);
        $pdo->exec("DROP DATABASE IF EXISTS `{$quotedDatabase}`");

        $this->temporaryDatabaseName = null;
        $this->testingDatabaseConfig = [];
    }

    public function test_employee_can_check_in_and_check_out_and_hr_receives_notifications(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 8, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee One');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR One');

        $this->actingAs($employee)
            ->post(route('attendance.check-in'))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $record = AttendanceRecord::query()->first();

        $this->assertNotNull($record);
        $this->assertNotNull($record->check_in_at);
        $this->assertSame('on_time', $record->attendance_status);
        $this->assertDatabaseHas('attendance_events', [
            'attendance_record_id' => $record->id,
            'event_type' => 'check_in',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $hr->id,
            'reference_id' => $record->id,
            'category' => 'attendance',
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.check-out'))
            ->assertRedirect();

        $record->refresh();

        $this->assertNotNull($record->check_out_at);
        $this->assertSame('pending', $record->approval_status);
        $this->assertFalse((bool) $record->is_confirmed);
        $this->assertGreaterThan(0, AttendanceEvent::query()->where('attendance_record_id', $record->id)->count());
        $this->assertGreaterThanOrEqual(2, Notification::query()->where('user_id', $hr->id)->count());

        Carbon::setTestNow();
    }

    public function test_work_shift_catalog_rejects_invalid_minutes(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Catalog Shift');

        $this->actingAs($admin)
            ->post(route('attendance.catalogs.work-shifts.store'), [
                'shift_code' => 'BAD',
                'shift_name' => 'Ca sai phut',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'break_start_time' => '12:00',
                'break_end_time' => '13:00',
                'standard_minutes' => 600,
                'half_day_minutes' => 240,
                'grace_minutes' => 0,
                'late_grace_minutes' => 0,
                'early_leave_grace_minutes' => 0,
                'allows_overtime' => true,
                'is_overnight' => false,
                'is_active' => true,
            ])
            ->assertSessionHasErrors('standard_minutes');
    }

    public function test_work_shift_catalog_rejects_standard_minutes_that_do_not_match_net_shift_minutes(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Catalog Standard Mismatch');

        $this->actingAs($admin)
            ->post(route('attendance.catalogs.work-shifts.store'), [
                'shift_name' => 'Ca lech phut chuan',
                'start_time' => '08:00',
                'end_time' => '17:30',
                'break_start_time' => '12:00',
                'break_end_time' => '13:00',
                'standard_minutes' => 480,
                'half_day_minutes' => 240,
                'handover_break_minutes' => 0,
                'grace_minutes' => 20,
                'allows_overtime' => true,
                'is_overnight' => false,
                'is_active' => true,
            ])
            ->assertSessionHasErrors('standard_minutes');
    }

    public function test_work_shift_catalog_rejects_gap_between_shift_end_and_overtime_start(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Catalog OT Gap');

        $this->actingAs($admin)
            ->post(route('attendance.catalogs.work-shifts.store'), [
                'shift_name' => 'Ca ho OT',
                'start_time' => '08:00',
                'end_time' => '17:30',
                'break_start_time' => '12:00',
                'break_end_time' => '13:00',
                'overtime_start_time' => '18:00',
                'overtime_end_time' => '20:30',
                'standard_minutes' => 510,
                'half_day_minutes' => 240,
                'handover_break_minutes' => 0,
                'overtime_hourly_rate' => 50000,
                'grace_minutes' => 20,
                'allows_overtime' => true,
                'is_overnight' => false,
                'is_active' => true,
            ])
            ->assertSessionHasErrors('overtime_start_time');
    }

    public function test_work_shift_catalog_stores_overtime_rule_in_separate_table(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Catalog OT Rule');

        $this->actingAs($admin)
            ->post(route('attendance.catalogs.work-shifts.store'), [
                'shift_name' => 'Ca tach quy tac tang ca',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'break_start_time' => '12:00',
                'break_end_time' => '13:00',
                'overtime_start_time' => '17:00',
                'overtime_end_time' => '21:00',
                'standard_minutes' => 480,
                'half_day_minutes' => 240,
                'handover_break_minutes' => 15,
                'overtime_hourly_rate' => 60000,
                'grace_minutes' => 10,
                'allows_overtime' => true,
                'is_overnight' => false,
                'is_active' => true,
            ])
            ->assertSessionHasNoErrors();

        $shift = WorkShift::query()->where('shift_name', 'Ca tach quy tac tang ca')->firstOrFail();

        $this->assertDatabaseHas('work_shift_overtime_rules', [
            'work_shift_id' => $shift->id,
            'start_time' => '17:00:00',
            'end_time' => '21:00:00',
            'hourly_rate' => 60000,
        ]);
    }

    public function test_check_out_subtracts_mid_shift_break_but_not_handover_break_from_worked_minutes(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 8, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Break Shift');

        $shift = WorkShift::query()->create([
            'shift_code' => 'CA001',
            'shift_name' => 'Ca co nghi giao ca',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'handover_break_minutes' => 30,
            'allows_overtime' => true,
            'is_active' => true,
        ]);

        EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'effective_from' => '2026-04-01',
            'is_active' => true,
            'created_by' => $employee->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.check-in'))
            ->assertRedirect();

        Carbon::setTestNow(Carbon::create(2026, 4, 15, 17, 0, 0, 'Asia/Ho_Chi_Minh'));

        $this->actingAs($employee)
            ->post(route('attendance.check-out'))
            ->assertRedirect();

        $record = AttendanceRecord::query()->firstOrFail();

        $this->assertSame(480, (int) $record->worked_minutes);
        $this->assertSame(0, (int) $record->overtime_minutes);
    }

    public function test_approved_overtime_does_not_subtract_handover_break_twice(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee OT Handover');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR OT Handover');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        $shift = WorkShift::query()->create([
            'shift_code' => 'CA-HANDOVER-OT',
            'shift_name' => 'Ca co nghi giao ca va OT',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'handover_break_minutes' => 30,
            'allows_overtime' => true,
            'is_active' => true,
        ]);

        $shift->overtimeRule()->create([
            'start_time' => '17:00:00',
            'end_time' => '18:00:00',
            'hourly_rate' => 50000,
        ]);

        $employee->employeeProfile->update([
            'hire_date' => '2026-04-01',
            'default_work_shift_id' => $shift->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'start_at' => '2026-04-15 17:00:00',
                'end_at' => '2026-04-15 18:00:00',
                'reason' => 'Tang ca sau ca co nghi giao ca',
            ])
            ->assertRedirect();

        $approvalRequest = ApprovalRequest::query()->where('request_type', 'overtime')->latest('id')->firstOrFail();

        $this->assertDatabaseHas('overtime_requests', [
            'id' => $approvalRequest->target_id,
            'requested_minutes' => 60,
            'status' => 'pending',
        ]);

        $this->actingAs($hr)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Duyet tang ca sau nghi giao ca',
            ])
            ->assertRedirect();

        $record = AttendanceRecord::query()
            ->where('employee_profile_id', $employee->employeeProfile->id)
            ->whereDate('work_date', '2026-04-15')
            ->firstOrFail();

        $this->assertSame($shift->id, (int) $record->work_shift_id);
        $this->assertSame(60, (int) $record->overtime_minutes);
    }

    public function test_work_shift_assignment_rejects_overlapping_target_ranges(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Catalog Assignment');
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Catalog Assignment');

        $shift = WorkShift::query()->create([
            'shift_code' => 'HC',
            'shift_name' => 'Ca hanh chinh',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'effective_from' => '2026-04-01',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('attendance.catalogs.assignments.store'), [
                'target_type' => 'employee',
                'employee_profile_id' => $employee->employeeProfile->id,
                'department_id' => Department::query()->create(['name' => 'Phong trung', 'is_active' => true])->id,
                'work_shift_id' => $shift->id,
                'effective_from' => '2026-04-10',
                'effective_to' => null,
                'weekdays' => [1, 2, 3, 4, 5],
                'is_active' => true,
            ])
            ->assertSessionHasErrors('employee_profile_id');
    }

    public function test_check_in_after_8am_is_marked_late(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 8, 20, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Late');

        $this->actingAs($employee)
            ->post(route('attendance.check-in'))
            ->assertRedirect();

        $record = AttendanceRecord::query()->first();

        $this->assertNotNull($record);
        $this->assertSame('late', $record->attendance_status);

        Carbon::setTestNow();
    }

    public function test_hr_can_confirm_attendance_record(): void
    {
        Queue::fake();

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Two');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Two');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(8),
            'check_out_at' => now()->startOfDay()->setHour(18),
            'worked_minutes' => 540,
            'attendance_status' => 'on_time',
            'is_confirmed' => false,
        ]);

        $this->actingAs($hr)
            ->post(route('attendance.confirm', $record), ['note' => 'Xac nhan hop le'])
            ->assertRedirect();

        $record->refresh();

        $this->assertTrue($record->is_confirmed);
        $this->assertSame('approved', $record->approval_status);
        $this->assertSame($hr->id, $record->confirmed_by);
        $this->assertDatabaseHas('attendance_approvals', [
            'attendance_record_id' => $record->id,
            'approved_by' => $hr->id,
            'status' => 'approved',
        ]);
        $this->assertSame(1, AttendanceApproval::query()->count());
        $this->assertDatabaseHas('approval_decision_deliveries', [
            'module' => 'attendance',
            'reference_type' => AttendanceRecord::class,
            'reference_id' => $record->id,
            'recipient_user_id' => $employee->id,
            'decision' => 'approved',
            'status' => 'queued',
        ]);
        Queue::assertPushed(SendApprovalDecisionEmailJob::class, 1);
    }

    public function test_reviewing_attendance_request_queues_email_notification_for_requester(): void
    {
        Queue::fake();

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Attendance Mail');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Attendance Mail');

        $leaveType = LeaveType::query()->create([
            'code' => 'MAIL_LEAVE',
            'name' => 'Nghi phep mail',
            'is_paid' => true,
            'deducts_balance' => false,
            'requires_attachment' => false,
            'is_active' => true,
        ]);

        $approvalRequest = ApprovalRequest::query()->create([
            'request_type' => 'leave',
            'target_type' => AttendanceRequest::class,
            'target_id' => 0,
            'requested_by' => $employee->id,
            'status' => 'pending',
            'reason' => 'Xin nghi phep de test mail',
            'submitted_at' => now(),
        ]);

        $attendanceRequest = AttendanceRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'approval_request_id' => $approvalRequest->id,
            'request_type' => 'leave',
            'leave_type_id' => $leaveType->id,
            'leave_type' => 'paid',
            'status' => 'pending',
            'from_date' => '2026-04-15',
            'to_date' => '2026-04-15',
            'reason' => 'Xin nghi phep de test mail',
        ]);

        $approvalRequest->update([
            'target_id' => $attendanceRequest->id,
        ]);

        app(\App\Services\AttendanceService::class)->reviewApprovalRequest(
            $approvalRequest->fresh('target'),
            $admin,
            'approved',
            'Duyet nghi phep gui mail'
        );

        $this->assertDatabaseHas('approval_decision_deliveries', [
            'module' => 'attendance',
            'reference_type' => ApprovalRequest::class,
            'reference_id' => $approvalRequest->id,
            'recipient_user_id' => $employee->id,
            'decision' => 'approved',
            'status' => 'queued',
        ]);
        Queue::assertPushed(SendApprovalDecisionEmailJob::class, 1);
    }

    public function test_approval_payload_includes_shift_and_standard_minutes(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Shift Payload');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Shift Payload');
        $shift = WorkShift::query()->create([
            'shift_code' => 'PAYLOAD',
            'shift_name' => 'Ca hanh chinh',
            'start_time' => '08:00:00',
            'end_time' => '17:30:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'handover_break_minutes' => 15,
            'allows_overtime' => true,
            'is_active' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(8),
            'check_out_at' => now()->startOfDay()->setHour(17)->setMinute(30),
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'is_confirmed' => false,
            'shift_snapshot' => [
                'shift_name' => 'Ca snapshot',
                'start_time' => '08:00:00',
                'end_time' => '17:30:00',
                'break_start_time' => '12:00:00',
                'break_end_time' => '13:00:00',
                'standard_minutes' => 480,
                'handover_break_minutes' => 15,
            ],
        ]);

        $payload = app(\App\Services\AttendanceService::class)->getApprovalsData([], $hr);
        $row = $payload['records']->first();

        $this->assertSame('Ca hanh chinh', $row['shift_name']);
        $this->assertSame('08:00', $row['shift_start_time']);
        $this->assertSame('17:30', $row['shift_end_time']);
        $this->assertSame(480, $row['standard_minutes']);
        $this->assertSame(60, $row['break_minutes']);
        $this->assertSame(15, $row['handover_break_minutes']);
    }

    public function test_approval_payload_links_attendance_request_type_on_record_row(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Linked Request');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Linked Request');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:00:00',
            'check_out_at' => '2026-04-14 16:30:00',
            'worked_minutes' => 450,
            'early_leave_minutes' => 30,
            'attendance_status' => 'on_time',
            'day_status' => 'early_leave',
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        AttendanceRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'request_type' => 'late_early',
            'requested_status' => 'early_leave',
            'status' => 'pending',
            'request_date' => '2026-04-14',
            'from_time' => '16:30',
            'to_time' => '17:30',
            'reason' => 'Can ve som',
            'applied_at' => now(),
        ]);

        $payload = app(\App\Services\AttendanceService::class)->getApprovalsData([], $hr);
        $row = $payload['records']->first();

        $this->assertSame('has_request', $row['request_presence']);
        $this->assertSame('Co don xin ve som', $row['request_presence_label']);
        $this->assertSame('xin ve som', $row['request_type_label']);
        $this->assertSame('late_early', $row['request_type']);
        $this->assertSame('pending', $row['request_status']);
        $this->assertSame('attendance', data_get($row, 'request_detail.target_type'));
        $this->assertSame('Can ve som', data_get($row, 'request_detail.reason'));
    }

    public function test_rejected_attendance_request_is_linked_on_record_row(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Rejected Request');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Rejected Request');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-22',
            'check_in_at' => '2026-04-22 08:05:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'late_minutes' => 0,
            'early_leave_minutes' => 0,
            'attendance_status' => 'on_time',
            'day_status' => 'missing_check_out',
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        AttendanceRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'request_type' => 'leave',
            'leave_type' => 'paid',
            'status' => 'rejected',
            'from_date' => '2026-04-22',
            'to_date' => '2026-04-24',
            'reason' => 'Can di bar',
            'applied_at' => now()->subDay(),
        ]);

        $payload = app(\App\Services\AttendanceService::class)->getApprovalsData([], $hr);
        $row = $payload['records']->first();

        $this->assertSame('has_request', $row['request_presence']);
        $this->assertSame('Co don xin nghi phep', $row['request_presence_label']);
        $this->assertSame('leave', $row['request_type']);
        $this->assertSame('rejected', $row['request_status']);
        $this->assertSame('attendance', data_get($row, 'request_detail.target_type'));
        $this->assertSame('Can di bar', data_get($row, 'request_detail.reason'));
    }

    public function test_hr_can_bulk_confirm_attendance_records(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Bulk');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Bulk');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        $records = collect([15, 16])->map(fn (int $day) => AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => Carbon::create(2026, 4, $day, 0, 0, 0, 'Asia/Ho_Chi_Minh')->toDateString(),
            'check_in_at' => Carbon::create(2026, 4, $day, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, $day, 17, 30, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]));

        $this->actingAs($hr)
            ->post(route('attendance.confirm-bulk'), [
                'record_ids' => $records->pluck('id')->all(),
                'note' => 'Duyet hang loat hop le',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        foreach ($records as $record) {
            $record->refresh();
            $this->assertTrue((bool) $record->is_confirmed);
            $this->assertSame('approved', $record->approval_status);
            $this->assertSame($hr->id, $record->confirmed_by);
        }

        $this->assertSame(2, AttendanceApproval::query()->count());
    }

    public function test_only_admin_can_confirm_hr_attendance_record(): void
    {
        $hrTarget = $this->makeUserWithAuthorityProfile('hr', 'HR Target');
        $hrApprover = $this->makeUserWithAuthorityProfile('hr', 'HR Approver');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin One');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $hrTarget->employeeProfile->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(8),
            'check_out_at' => now()->startOfDay()->setHour(18),
            'worked_minutes' => 540,
            'attendance_status' => 'on_time',
            'is_confirmed' => false,
        ]);

        $this->actingAs($hrApprover)
            ->from(route('attendance.approvals'))
            ->post(route('attendance.confirm', $record), ['note' => 'HR thu duyet'])
            ->assertSessionHasErrors(['error']);

        $record->refresh();
        $this->assertFalse($record->is_confirmed);

        $this->actingAs($admin)
            ->post(route('attendance.confirm', $record), ['note' => 'Admin duyet'])
            ->assertRedirect();

        $record->refresh();
        $this->assertTrue($record->is_confirmed);
        $this->assertSame($admin->id, $record->confirmed_by);
    }

    public function test_approval_page_hides_equal_or_higher_authority_records_and_requests(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Visible Approval');
        $hrTarget = $this->makeUserWithAuthorityProfile('hr', 'HR Hidden Approval');
        $adminTarget = $this->makeUserWithAuthorityProfile('admin', 'Admin Hidden Approval');
        $hrViewer = $this->makeUserWithAuthorityProfile('hr', 'HR Approval Viewer');
        $outsideEmployee = $this->makeUserWithAuthorityProfile('employee', 'Employee Outside Approval Scope');

        $employee->employeeProfile->update([
            'reports_to_user_id' => $hrViewer->id,
        ]);

        foreach ([$employee, $outsideEmployee, $hrTarget, $adminTarget] as $user) {
            AttendanceRecord::query()->create([
                'employee_profile_id' => $user->employeeProfile->id,
                'work_date' => '2026-04-15',
                'check_in_at' => '2026-04-15 08:00:00',
                'check_out_at' => '2026-04-15 17:00:00',
                'worked_minutes' => 480,
                'attendance_status' => 'on_time',
                'approval_status' => 'pending',
                'day_status' => 'present',
                'is_confirmed' => false,
            ]);
        }

        foreach ([$employee, $outsideEmployee, $hrTarget, $adminTarget] as $user) {
            $approvalRequest = ApprovalRequest::query()->create([
                'request_type' => 'forgot_check',
                'target_type' => AttendanceRequest::class,
                'target_id' => 0,
                'requested_by' => $user->id,
                'status' => 'pending',
                'reason' => 'Quen cham cong',
                'submitted_at' => now(),
            ]);

            $attendanceRequest = AttendanceRequest::query()->create([
                'employee_profile_id' => $user->employeeProfile->id,
                'approval_request_id' => $approvalRequest->id,
                'request_type' => 'forgot_check',
                'status' => 'pending',
                'request_date' => '2026-04-13',
                'reason' => 'Quen cham cong',
            ]);

            $approvalRequest->update(['target_id' => $attendanceRequest->id]);
        }

        $response = $this->actingAs($hrViewer)->get(route('attendance.approvals', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $recordNames = collect(data_get($page, 'props.records'))->pluck('employee_name')->all();
        $requestNames = collect(data_get($page, 'props.request_approvals'))->pluck('employee_name')->all();
        $employeeOptions = collect(data_get($page, 'props.employees'))->pluck('label')->join(' | ');

        $this->assertSame(['Employee Visible Approval'], $recordNames);
        $this->assertSame(['Employee Visible Approval'], $requestNames);
        $this->assertStringContainsString('Employee Visible Approval', $employeeOptions);
        $this->assertStringNotContainsString('Employee Outside Approval Scope', $employeeOptions);
        $this->assertStringNotContainsString('HR Hidden Approval', $employeeOptions);
        $this->assertStringNotContainsString('Admin Hidden Approval', $employeeOptions);
    }

    public function test_reviewer_cannot_approve_lower_authority_employee_outside_subordinate_scope(): void
    {
        $subordinate = $this->makeUserWithAuthorityProfile('employee', 'Employee Direct Subordinate');
        $outsideEmployee = $this->makeUserWithAuthorityProfile('employee', 'Employee Outside Subordinate Scope');
        $hrViewer = $this->makeUserWithAuthorityProfile('hr', 'HR Scoped Approver');

        $subordinate->employeeProfile->update([
            'reports_to_user_id' => $hrViewer->id,
        ]);

        $subordinateRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $subordinate->employeeProfile->id,
            'work_date' => '2026-04-16',
            'check_in_at' => '2026-04-16 08:00:00',
            'check_out_at' => '2026-04-16 17:30:00',
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'is_confirmed' => false,
        ]);

        $outsideRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $outsideEmployee->employeeProfile->id,
            'work_date' => '2026-04-16',
            'check_in_at' => '2026-04-16 08:00:00',
            'check_out_at' => '2026-04-16 17:30:00',
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'is_confirmed' => false,
        ]);

        $this->actingAs($hrViewer)
            ->from(route('attendance.approvals'))
            ->post(route('attendance.confirm', $outsideRecord), ['note' => 'Thu duyet ngoai pham vi'])
            ->assertSessionHasErrors(['error']);

        $outsideRecord->refresh();
        $this->assertFalse((bool) $outsideRecord->is_confirmed);

        $this->actingAs($hrViewer)
            ->post(route('attendance.confirm', $subordinateRecord), ['note' => 'Duyet cap duoi truc tiep'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $subordinateRecord->refresh();
        $this->assertTrue((bool) $subordinateRecord->is_confirmed);
        $this->assertSame($hrViewer->id, (int) $subordinateRecord->confirmed_by);
    }

    public function test_general_report_hides_equal_or_higher_authority_attendance_data(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Visible Report');
        $hrTarget = $this->makeUserWithAuthorityProfile('hr', 'HR Visible Report');
        $adminTarget = $this->makeUserWithAuthorityProfile('admin', 'Admin Hidden Report');
        $adminViewer = $this->makeUserWithAuthorityProfile('admin', 'Admin Report Viewer');

        foreach ([$employee, $hrTarget, $adminTarget] as $user) {
            AttendanceRecord::query()->create([
                'employee_profile_id' => $user->employeeProfile->id,
                'work_date' => '2026-04-15',
                'check_in_at' => '2026-04-15 08:00:00',
                'check_out_at' => '2026-04-15 17:00:00',
                'worked_minutes' => 480,
                'attendance_status' => 'on_time',
                'approval_status' => 'approved',
                'day_status' => 'present',
                'is_confirmed' => true,
            ]);
        }

        $response = $this->actingAs($adminViewer)->get(route('reports.index', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');

        $this->assertSame('Du lieu cap duoi', data_get($page, 'props.scopeLabel'));
        $this->assertFalse((bool) data_get($page, 'props.canViewAll'));
        $this->assertSame(2, (int) data_get($page, 'props.attendanceMonthly.total_records'));
        $this->assertSame(2, collect(data_get($page, 'props.employeeByDepartment'))->sum('employee_count'));
    }

    public function test_attendance_report_hides_equal_or_higher_authority_people(): void
    {
        $hrViewer = $this->makeUserWithAuthorityProfile('hr', 'HR Attendance Report Viewer');
        $visibleEmployee = $this->makeUserWithAuthorityProfile('employee', 'Employee Visible Attendance Report');
        $equalHr = $this->makeUserWithAuthorityProfile('hr', 'HR Hidden Equal Attendance Report');
        $higherAdmin = $this->makeUserWithAuthorityProfile('admin', 'Admin Hidden Higher Attendance Report');

        foreach ([$visibleEmployee, $equalHr, $higherAdmin] as $target) {
            $target->employeeProfile->update([
                'reports_to_user_id' => $hrViewer->id,
            ]);

            AttendanceRecord::query()->create([
                'employee_profile_id' => $target->employeeProfile->id,
                'work_date' => '2026-04-15',
                'check_in_at' => '2026-04-15 08:00:00',
                'check_out_at' => '2026-04-15 17:30:00',
                'worked_minutes' => 480,
                'attendance_status' => 'on_time',
                'approval_status' => 'approved',
                'day_status' => 'present',
                'missing_check_in' => false,
                'missing_check_out' => false,
                'is_confirmed' => true,
            ]);

            OvertimeRequest::query()->create([
                'employee_profile_id' => $target->employeeProfile->id,
                'work_date' => '2026-04-15',
                'start_at' => '2026-04-15 17:30:00',
                'end_at' => '2026-04-15 19:30:00',
                'requested_minutes' => 120,
                'approved_minutes' => 120,
                'status' => 'approved',
                'reason' => 'Tang ca bao cao',
                'requested_by' => $target->id,
            ]);
        }

        $response = $this->actingAs($hrViewer)->get(route('attendance.reports', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $recordNames = collect(data_get($page, 'props.records', []))->pluck('employee_name')->all();
        $employeeLabels = collect(data_get($page, 'props.employees', []))->pluck('label')->implode('|');
        $overtimeNames = collect(data_get($page, 'props.overtime_details', []))->pluck('employee_name')->all();

        $this->assertSame(1, (int) data_get($page, 'props.summary.total_records'));
        $this->assertContains($visibleEmployee->name, $recordNames);
        $this->assertNotContains($equalHr->name, $recordNames);
        $this->assertNotContains($higherAdmin->name, $recordNames);
        $this->assertStringContainsString($visibleEmployee->name, $employeeLabels);
        $this->assertStringNotContainsString($equalHr->name, $employeeLabels);
        $this->assertStringNotContainsString($higherAdmin->name, $employeeLabels);
        $this->assertContains($visibleEmployee->name, $overtimeNames);
        $this->assertNotContains($equalHr->name, $overtimeNames);
        $this->assertNotContains($higherAdmin->name, $overtimeNames);

        $forcedHigherFilter = $this->actingAs($hrViewer)->get(route('attendance.reports', [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $higherAdmin->employeeProfile->id,
        ]));

        $forcedHigherFilter->assertOk();
        $forcedPage = $forcedHigherFilter->viewData('page');

        $this->assertSame(0, count(data_get($forcedPage, 'props.records', [])));
        $this->assertSame(0, count(data_get($forcedPage, 'props.overtime_details', [])));
    }

    public function test_attendance_report_synthesizes_absent_rows_for_missing_past_working_days(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 24, 8, 39, 0, 'Asia/Ho_Chi_Minh'));

        $hrViewer = $this->makeUserWithAuthorityProfile('hr', 'HR Missing Absent Viewer');
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Missing Absent Row');

        $employee->employeeProfile->update([
            'reports_to_user_id' => $hrViewer->id,
            'hire_date' => '2026-04-23',
        ]);

        $response = $this->actingAs($hrViewer)->get(route('attendance.reports', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $records = collect(data_get($page, 'props.records', []));
        $absentRow = $records->first(fn (array $row) =>
            data_get($row, 'employee_name') === $employee->name
            && data_get($row, 'work_date') === '2026-04-23'
        );

        $this->assertNotNull($absentRow);
        $this->assertSame('absent', data_get($absentRow, 'day_status'));
        $this->assertSame('missing_attendance', data_get($absentRow, 'violation_status'));
        $this->assertSame('needs_verification', data_get($absentRow, 'display_approval_status'));

        $this->assertDatabaseMissing('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-23',
        ]);
    }

    public function test_dashboard_counts_only_subordinate_attendance_and_pending_approvals(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Visible Dashboard');
        $hrTarget = $this->makeUserWithAuthorityProfile('hr', 'HR Visible Dashboard');
        $adminTarget = $this->makeUserWithAuthorityProfile('admin', 'Admin Hidden Dashboard');
        $adminViewer = $this->makeUserWithAuthorityProfile('admin', 'Admin Dashboard Viewer');

        foreach ([$employee, $hrTarget, $adminTarget] as $user) {
            AttendanceRecord::query()->create([
                'employee_profile_id' => $user->employeeProfile->id,
                'work_date' => '2026-04-14',
                'check_in_at' => '2026-04-14 09:00:00',
                'check_out_at' => '2026-04-14 17:00:00',
                'worked_minutes' => 420,
                'attendance_status' => 'late',
                'approval_status' => 'pending',
                'day_status' => 'late',
                'late_minutes' => 60,
                'is_confirmed' => false,
            ]);
        }

        $response = $this->actingAs($adminViewer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');

        $this->assertSame(2, (int) data_get($page, 'props.dashboardSummary.total_employees'));
        $this->assertSame(2, (int) data_get($page, 'props.dashboardSummary.attendance_month_report.total_records'));
        $this->assertStringContainsString('2', (string) data_get($page, 'props.warnings.0.message'));
        $this->assertStringNotContainsString('3', (string) data_get($page, 'props.warnings.0.message'));
    }

    public function test_dashboard_pending_approval_warning_matches_approval_page_scope(): void
    {
        $systemOwner = $this->makeUserWithAuthorityProfile('admin', 'System Owner Pending Self');
        $systemOwner->update(['email' => 'gtvbehieu@gmail.com']);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $systemOwner->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 09:00:00',
            'check_out_at' => '2026-04-14 17:00:00',
            'worked_minutes' => 420,
            'attendance_status' => 'late',
            'approval_status' => 'pending',
            'day_status' => 'late',
            'late_minutes' => 60,
            'is_confirmed' => false,
        ]);

        $dashboardResponse = $this->actingAs($systemOwner)->get(route('dashboard'));
        $approvalResponse = $this->actingAs($systemOwner)->get(route('attendance.approvals'));

        $dashboardResponse->assertOk();
        $dashboardResponse->assertViewHas('page');
        $approvalResponse->assertOk();
        $approvalResponse->assertViewHas('page');

        $dashboardPage = $dashboardResponse->viewData('page');
        $approvalPage = $approvalResponse->viewData('page');
        $dashboardApprovalWarnings = collect(data_get($dashboardPage, 'props.warnings', []))
            ->where('cta_url', route('attendance.approvals'))
            ->values();

        $this->assertSame(0, (int) data_get($approvalPage, 'props.approval_summary.pending_records'));
        $this->assertSame(0, count(data_get($approvalPage, 'props.request_approvals', [])));
        $this->assertTrue($dashboardApprovalWarnings->isEmpty());
    }

    public function test_system_operator_account_has_self_service_attendance_and_salary_hidden(): void
    {
        $systemOwner = $this->makeUserWithAuthorityProfile('admin', 'System Operator Hidden Self Service');
        $systemOwner->update(['email' => 'gtvbehieu@gmail.com']);

        $capabilityIds = \App\Models\PositionCapability::query()
            ->whereIn('code', [
                Capability::CHECK_IN,
                Capability::CHECK_OUT,
                Capability::VIEW_OWN_ATTENDANCE,
                Capability::VIEW_OWN_SALARY,
                Capability::REQUEST_ATTENDANCE_ADJUSTMENT,
            ])
            ->pluck('id');

        foreach ($capabilityIds as $capabilityId) {
            DB::table('user_position_capability_overrides')->updateOrInsert(
                [
                    'user_id' => $systemOwner->id,
                    'capability_id' => $capabilityId,
                ],
                [
                    'effect' => 'deny',
                    'reason' => 'Test deny self-service capabilities.',
                    'expires_at' => null,
                    'created_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $systemOwner = $systemOwner->fresh('employeeProfile.position.capabilitiesCatalog');
        $permissions = \App\Support\AccessMatrix::permissionsFor($systemOwner);
        $menuPaths = collect(\App\Support\MenuBuilder::build($systemOwner))
            ->flatMap(fn (array $group) => $group['items'] ?? [])
            ->pluck('path')
            ->all();

        $this->assertFalse($systemOwner->hasPositionCapability(Capability::CHECK_IN));
        $this->assertFalse($systemOwner->hasPositionCapability(Capability::CHECK_OUT));
        $this->assertFalse($systemOwner->hasPositionCapability(Capability::VIEW_OWN_ATTENDANCE));
        $this->assertFalse($systemOwner->hasPositionCapability(Capability::VIEW_OWN_SALARY));
        $this->assertFalse($systemOwner->hasPositionCapability(Capability::REQUEST_ATTENDANCE_ADJUSTMENT));
        $this->assertFalse($permissions['attendance.mine.view']);
        $this->assertFalse($permissions['attendance.mine.action']);
        $this->assertNotContains('/my-attendance', $menuPaths);
        $this->assertNotContains('/my-leave', $menuPaths);
        $this->assertNotContains('/my-salary', $menuPaths);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $systemOwner->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 09:00:00',
            'check_out_at' => '2026-04-14 17:00:00',
            'worked_minutes' => 420,
            'attendance_status' => 'late',
            'approval_status' => 'approved',
            'day_status' => 'late',
            'late_minutes' => 60,
            'is_confirmed' => false,
        ]);

        $dashboardResponse = $this->actingAs($systemOwner)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardPage = $dashboardResponse->viewData('page');
        $warningUrls = collect(data_get($dashboardPage, 'props.warnings', []))->pluck('cta_url')->all();

        $this->assertNull(data_get($dashboardPage, 'props.todayAttendance'));
        $this->assertNotContains(route('attendance.mine'), $warningUrls);

        $this->actingAs($systemOwner)
            ->getJson(route('attendance.mine'))
            ->assertForbidden();

        $this->actingAs($systemOwner)
            ->getJson(route('leave.mine'))
            ->assertForbidden();

        $this->actingAs($systemOwner)
            ->getJson(route('salary.mine'))
            ->assertForbidden();

        $this->actingAs($systemOwner)
            ->postJson(route('attendance.check-in'))
            ->assertForbidden();
    }

    public function test_employee_dashboard_uses_personal_reconciled_attendance_status(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Dashboard Leave');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'worked_minutes' => 480,
            'attendance_status' => 'absent',
            'approval_status' => 'approved',
            'day_status' => 'leave',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $stats = collect(data_get($page, 'props.stats'));

        $this->assertSame('leave', data_get($page, 'props.todayAttendance.day_status'));
        $this->assertSame('Nghi phep', data_get($page, 'props.todayAttendance.status_label'));
        $this->assertSame('1', (string) data_get($stats->firstWhere('title', 'Cong thang nay'), 'value'));
        $this->assertSame('personal', data_get($page, 'props.dashboardSummary.attendance_month_report.scope'));
        $this->assertSame(1, (int) data_get($page, 'props.dashboardSummary.attendance_month_report.total_records'));
        $this->assertSame(1, (int) data_get($page, 'props.dashboardSummary.attendance_month_report.leave_records'));
        $this->assertSame(0, (int) data_get($page, 'props.dashboardSummary.attendance_month_report.absent_records'));
        $this->assertSame(480, (int) data_get($page, 'props.dashboardSummary.attendance_month_report.total_worked_minutes'));
    }

    public function test_employee_dashboard_exposes_partial_leave_duration_for_check_in_rules(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Dashboard Half Leave');
        $leaveType = LeaveType::query()->create([
            'code' => 'HALF-DASH',
            'name' => 'Nghi nua ngay dashboard',
            'is_paid' => true,
            'deducts_balance' => false,
            'requires_attachment' => false,
            'is_active' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'worked_minutes' => 240,
            'attendance_status' => 'absent',
            'approval_status' => 'approved',
            'day_status' => 'leave',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        AttendanceRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'request_type' => 'leave',
            'leave_type_id' => $leaveType->id,
            'leave_type' => 'paid',
            'status' => 'approved',
            'from_date' => '2026-04-14',
            'to_date' => '2026-04-14',
            'leave_days' => 0.5,
            'leave_duration_type' => 'half_day',
            'leave_hours' => 0,
            'reason' => 'Nghi nua ngay',
            'applied_at' => now(),
            'applied_by' => $employee->id,
        ]);

        $response = $this->actingAs($employee)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $stats = collect(data_get($page, 'props.stats'));

        $this->assertSame('leave', data_get($page, 'props.todayAttendance.day_status'));
        $this->assertSame('half_day', data_get($page, 'props.todayAttendance.leave_duration_type'));
        $this->assertSame(0.5, (float) data_get($page, 'props.todayAttendance.leave_days'));
        $this->assertSame('0.5', (string) data_get($stats->firstWhere('title', 'Cong thang nay'), 'value'));
        $this->assertSame(240, (int) data_get($page, 'props.dashboardSummary.attendance_month_report.total_worked_minutes'));
    }

    public function test_hr_can_reject_attendance_record(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Reject');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Reject');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(9),
            'attendance_status' => 'late',
            'approval_status' => 'pending',
            'day_status' => 'missing_check_out',
            'is_confirmed' => false,
        ]);

        $this->actingAs($hr)
            ->post(route('attendance.reject', $record), ['note' => 'Thiếu dữ liệu checkout'])
            ->assertRedirect();

        $record->refresh();

        $this->assertSame('rejected', $record->approval_status);
        $this->assertFalse($record->is_confirmed);
        $this->assertSame($hr->id, $record->rejected_by);
        $this->assertDatabaseHas('attendance_approvals', [
            'attendance_record_id' => $record->id,
            'approved_by' => $hr->id,
            'status' => 'rejected',
        ]);
    }

    public function test_employee_only_sees_own_attendance_page_data(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Three');
        $otherEmployee = $this->makeUserWithAuthorityProfile('employee', 'Employee Four');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(8),
            'check_out_at' => now()->startOfDay()->setHour(17),
            'worked_minutes' => 540,
            'attendance_status' => 'on_time',
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $otherEmployee->employeeProfile->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(9),
            'check_out_at' => now()->startOfDay()->setHour(17),
            'worked_minutes' => 480,
            'attendance_status' => 'late',
        ]);

        $response = $this->actingAs($employee)->get(route('attendance.mine'));

        $response->assertOk();
        $response->assertSee($employee->employeeProfile->employee_code);
        $response->assertDontSee($otherEmployee->employeeProfile->employee_code);
    }

    public function test_my_attendance_summary_uses_work_unit_rules(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Work Unit');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-10',
            'check_in_at' => Carbon::create(2026, 4, 10, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 10, 16, 0, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-11',
            'check_in_at' => Carbon::create(2026, 4, 11, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 11, 12, 0, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 240,
            'attendance_status' => 'late',
            'day_status' => 'late',
            'approval_status' => 'pending',
            'missing_check_in' => false,
            'missing_check_out' => false,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-12',
            'check_in_at' => Carbon::create(2026, 4, 12, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 12, 11, 59, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 239,
            'attendance_status' => 'late',
            'day_status' => 'early_leave',
            'approval_status' => 'pending',
            'missing_check_in' => false,
            'missing_check_out' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('attendance.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame(1.5, (float) data_get($page, 'props.summary.total_work_units'));
    }

    public function test_my_attendance_reconciles_overtime_and_does_not_double_count_approved_minutes(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Overtime Reconcile');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
            'check_in_at' => Carbon::create(2026, 4, 20, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 20, 17, 52, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 592,
            'overtime_minutes' => 592,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        OvertimeRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
            'start_at' => '2026-04-20 17:30:00',
            'end_at' => '2026-04-20 19:22:00',
            'requested_minutes' => 112,
            'approved_minutes' => 112,
            'status' => 'approved',
            'reason' => 'Tang ca da duyet',
            'requested_by' => $employee->id,
        ]);

        $response = $this->actingAs($employee)->get(route('attendance.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame(592, (int) data_get($page, 'props.summary.approved_worked_minutes'));
        $this->assertSame(112, (int) data_get($page, 'props.summary.total_overtime_minutes'));
        $this->assertSame(112, (int) data_get($page, 'props.records.0.overtime_minutes'));
    }

    public function test_my_attendance_exposes_closed_period_flags(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Closed Attendance Period');
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Closed Attendance Period');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
            'check_in_at' => '2026-04-20 08:00:00',
            'check_out_at' => '2026-04-20 12:00:00',
            'worked_minutes' => 240,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        AttendanceMonthLock::query()->create([
            'month' => 4,
            'year' => 2026,
            'department_id' => null,
            'is_locked' => true,
            'locked_at' => now('Asia/Ho_Chi_Minh'),
            'locked_by' => $admin->id,
            'note' => 'Chot cong thang 4',
        ]);

        $period = PayrollPeriod::query()->create([
            'month' => 4,
            'year' => 2026,
            'status' => 'locked',
            'locked_at' => now('Asia/Ho_Chi_Minh'),
            'locked_by' => $admin->id,
        ]);

        SalarySnapshot::query()->create([
            'payroll_period_id' => $period->id,
            'employee_profile_id' => $employee->employeeProfile->id,
            'employee_name' => $employee->name,
            'payload' => [],
        ]);

        $response = $this->actingAs($employee)->get(route('attendance.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertTrue((bool) data_get($page, 'props.month_lock.is_locked'));
        $this->assertTrue((bool) data_get($page, 'props.payroll_period.is_locked'));
        $this->assertSame('locked', data_get($page, 'props.payroll_period.status'));
        $this->assertSame(1, (int) data_get($page, 'props.payroll_period.snapshot_count'));
    }

    public function test_employee_can_view_personal_salary_statement(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Statement');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-01',
            'check_in_at' => Carbon::create(2026, 4, 1, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 1, 17, 0, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 480,
            'overtime_minutes' => 60,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
        ]);

        OvertimeRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-01',
            'start_at' => '2026-04-01 17:00:00',
            'end_at' => '2026-04-01 18:00:00',
            'requested_minutes' => 60,
            'approved_minutes' => 60,
            'status' => 'approved',
            'reason' => 'Tang ca da duyet',
            'requested_by' => $employee->id,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-02',
            'check_in_at' => Carbon::create(2026, 4, 2, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 2, 12, 0, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 240,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'pending',
            'missing_check_in' => false,
            'missing_check_out' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame(22000000.0, (float) data_get($page, 'props.summary.base_salary'));
        $this->assertSame(1.0, (float) data_get($page, 'props.summary.approved_work_units'));
        $this->assertSame(0.5, (float) data_get($page, 'props.summary.pending_work_units'));
        $this->assertSame(60, (int) data_get($page, 'props.summary.approved_overtime_minutes'));
        $this->assertSame(412500.0, (float) data_get($page, 'props.summary.overtime_amount'));
        $this->assertSame(1.5, (float) data_get($page, 'props.records.0.overtime_multiplier'));
        $this->assertSame('Ngay thuong', data_get($page, 'props.records.0.overtime_type_label'));
    }

    public function test_employee_can_export_personal_salary_pdf(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary PDF');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-01',
            'check_in_at' => Carbon::create(2026, 4, 1, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 1, 17, 30, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 510,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine.export.pdf', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment; filename="payslip-', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_project_list_is_paginated_server_side(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Project Pagination');

        foreach (range(1, 15) as $index) {
            Project::query()->create([
                'name' => 'Pagination Project ' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'start_date' => '2026-04-01',
                'status' => 'planning',
                'description' => 'Project pagination test',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        $firstPage = $this->actingAs($admin)->get(route('projects.index', [
            'per_page' => 10,
        ]));

        $firstPage->assertOk();
        $firstPayload = $firstPage->viewData('page');
        $this->assertCount(10, data_get($firstPayload, 'props.projects'));
        $this->assertSame(15, (int) data_get($firstPayload, 'props.pagination.total'));
        $this->assertSame(2, (int) data_get($firstPayload, 'props.pagination.last_page'));
        $this->assertSame(1, (int) data_get($firstPayload, 'props.pagination.current_page'));

        $secondPage = $this->actingAs($admin)->get(route('projects.index', [
            'per_page' => 10,
            'page' => 2,
        ]));

        $secondPage->assertOk();
        $secondPayload = $secondPage->viewData('page');
        $this->assertCount(5, data_get($secondPayload, 'props.projects'));
        $this->assertSame(2, (int) data_get($secondPayload, 'props.pagination.current_page'));
    }

    public function test_salary_statement_counts_grace_covered_early_leave_as_full_work_unit(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Grace');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        $shift = WorkShift::query()->create([
            'shift_name' => 'Ca co grace ve som',
            'start_time' => '08:00:00',
            'end_time' => '17:30:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 510,
            'half_day_minutes' => 255,
            'handover_break_minutes' => 0,
            'grace_minutes' => 20,
            'late_grace_minutes' => 20,
            'early_leave_grace_minutes' => 20,
            'allows_overtime' => true,
            'is_overnight' => false,
            'is_active' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'work_date' => '2026-04-20',
            'check_in_at' => Carbon::create(2026, 4, 20, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 20, 17, 15, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 495,
            'late_minutes' => 0,
            'early_leave_minutes' => 10,
            'attendance_status' => 'on_time',
            'day_status' => 'early_leave',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame(1.0, (float) data_get($page, 'props.summary.approved_work_units'));
        $this->assertSame(1.0, (float) data_get($page, 'props.records.0.work_unit'));
        $this->assertSame('present', data_get($page, 'props.records.0.day_status'));
    }

    public function test_salary_statement_ignores_overtime_without_approved_overtime_request(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Unapproved OT');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-03',
            'worked_minutes' => 540,
            'overtime_minutes' => 60,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame(0, (int) data_get($page, 'props.summary.approved_overtime_minutes'));
        $this->assertSame(0.0, (float) data_get($page, 'props.summary.overtime_amount'));
        $this->assertSame(0, (int) data_get($page, 'props.records.0.overtime_minutes'));
    }

    public function test_salary_statement_counts_paid_holiday_without_attendance_as_one_work_unit(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Paid Holiday');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        Holiday::query()->create([
            'holiday_date' => '2026-04-20',
            'holiday_name' => 'Gio to Hung Vuong',
            'holiday_type' => 'public',
            'is_paid_leave' => true,
            'is_recurring' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame(1.0, (float) data_get($page, 'props.summary.approved_work_units'));
        $this->assertSame('holiday_paid', data_get($page, 'props.records.0.day_status'));
        $this->assertSame(1.0, (float) data_get($page, 'props.records.0.work_unit'));
        $this->assertSame('approved', data_get($page, 'props.records.0.approval_status'));
    }

    public function test_salary_statement_does_not_count_unpaid_holiday_without_attendance(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Unpaid Holiday');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        Holiday::query()->create([
            'holiday_date' => '2026-04-20',
            'holiday_name' => 'Ngay nghi khong luong',
            'holiday_type' => 'company',
            'is_paid_leave' => false,
            'is_recurring' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame(0.0, (float) data_get($page, 'props.summary.approved_work_units'));
        $this->assertSame([], data_get($page, 'props.records'));
    }

    public function test_salary_overtime_amount_uses_weekday_and_weekend_multipliers(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Overtime Rates');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-03',
            'worked_minutes' => 480,
            'overtime_minutes' => 60,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        OvertimeRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-03',
            'start_at' => '2026-04-03 17:00:00',
            'end_at' => '2026-04-03 18:00:00',
            'requested_minutes' => 60,
            'approved_minutes' => 60,
            'status' => 'approved',
            'reason' => 'Tang ca ngay thuong',
            'requested_by' => $employee->id,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-04',
            'worked_minutes' => 480,
            'overtime_minutes' => 60,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        OvertimeRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-04',
            'start_at' => '2026-04-04 17:00:00',
            'end_at' => '2026-04-04 18:00:00',
            'requested_minutes' => 60,
            'approved_minutes' => 60,
            'status' => 'approved',
            'reason' => 'Tang ca cuoi tuan',
            'requested_by' => $employee->id,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame(2.0, (float) data_get($page, 'props.summary.approved_work_units'));
        $this->assertSame(120, (int) data_get($page, 'props.summary.approved_overtime_minutes'));
        $this->assertSame(962500.0, (float) data_get($page, 'props.summary.overtime_amount'));
        $this->assertSame(1.5, (float) data_get($page, 'props.records.0.overtime_multiplier'));
        $this->assertSame('Ngay thuong', data_get($page, 'props.records.0.overtime_type_label'));
        $this->assertSame(412500.0, (float) data_get($page, 'props.records.0.overtime_amount'));
        $this->assertSame(2.0, (float) data_get($page, 'props.records.1.overtime_multiplier'));
        $this->assertSame('Ngay nghi tuan', data_get($page, 'props.records.1.overtime_type_label'));
        $this->assertSame(550000.0, (float) data_get($page, 'props.records.1.overtime_amount'));
    }

    public function test_salary_overtime_amount_uses_shift_configured_hourly_rate_when_present(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Shift Rate');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => '2026-04-15 08:00:00',
            'check_out_at' => '2026-04-15 19:00:00',
            'worked_minutes' => 480,
            'overtime_minutes' => 120,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
            'shift_snapshot' => [
                'standard_minutes' => 480,
                'half_day_minutes' => 240,
                'overtime_hourly_rate' => 50000,
            ],
        ]);

        OvertimeRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'start_at' => '2026-04-15 17:00:00',
            'end_at' => '2026-04-15 19:00:00',
            'requested_minutes' => 120,
            'approved_minutes' => 120,
            'status' => 'approved',
            'reason' => 'Tang ca co don duyet',
            'requested_by' => $employee->id,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame(100000.0, (float) data_get($page, 'props.summary.overtime_amount'));
        $this->assertSame(100000.0, (float) data_get($page, 'props.records.0.overtime_amount'));
    }

    public function test_salary_overtime_amount_does_not_subtract_handover_break_twice(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Handover OT');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => '2026-04-15 08:00:00',
            'check_out_at' => '2026-04-15 17:00:00',
            'worked_minutes' => 480,
            'overtime_minutes' => 60,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
            'shift_snapshot' => [
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_start_time' => '12:00:00',
                'break_end_time' => '13:00:00',
                'standard_minutes' => 480,
                'half_day_minutes' => 240,
                'handover_break_minutes' => 30,
                'overtime_start_time' => '17:00:00',
                'overtime_end_time' => '18:00:00',
                'overtime_hourly_rate' => 50000,
                'allows_overtime' => true,
            ],
        ]);

        OvertimeRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'start_at' => '2026-04-15 17:00:00',
            'end_at' => '2026-04-15 18:00:00',
            'requested_minutes' => 60,
            'approved_minutes' => 60,
            'status' => 'approved',
            'reason' => 'Tang ca sau ca co nghi giao ca',
            'requested_by' => $employee->id,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame(60, (int) data_get($page, 'props.summary.approved_overtime_minutes'));
        $this->assertSame(50000.0, (float) data_get($page, 'props.summary.overtime_amount'));
        $this->assertSame(50000.0, (float) data_get($page, 'props.records.0.overtime_amount'));
    }

    public function test_company_salary_detail_exposes_checkin_and_checkout_times(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Salary Company Detail');
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Company Detail');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => '2026-04-15 08:05:00',
            'check_out_at' => '2026-04-15 17:10:00',
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('salary.company', [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame('08:05', data_get($page, 'props.selectedDetail.records.0.check_in_at'));
        $this->assertSame('17:10', data_get($page, 'props.selectedDetail.records.0.check_out_at'));
    }

    public function test_locked_salary_period_uses_snapshot_after_live_data_changes(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Salary Lock');
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Lock');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => '2026-04-15 08:00:00',
            'check_out_at' => '2026-04-15 17:00:00',
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('salary.company.lock'), [
                'month' => 4,
                'year' => 2026,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payroll_periods', [
            'month' => 4,
            'year' => 2026,
            'status' => 'locked',
        ]);
        $this->assertDatabaseHas('salary_snapshots', [
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $record->update([
            'check_out_at' => null,
            'missing_check_out' => true,
            'worked_minutes' => 60,
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame('locked', data_get($page, 'props.periodStatus.status'));
        $this->assertSame(1.0, (float) data_get($page, 'props.summary.approved_work_units'));
        $this->assertSame('17:00', data_get($page, 'props.records.0.check_out_at'));

        $period = PayrollPeriod::query()->where(['month' => 4, 'year' => 2026])->firstOrFail();
        $snapshot = SalarySnapshot::query()->where('payroll_period_id', $period->id)->where('employee_profile_id', $employee->employeeProfile->id)->firstOrFail();
        $this->assertSame('17:00', data_get($snapshot->payload, 'records.0.check_out_at'));
    }

    public function test_locked_salary_period_blocks_manual_adjustment_changes_until_unlocked(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Locked Salary Adjustment');
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Locked Salary Adjustment');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => '2026-04-15 08:00:00',
            'check_out_at' => '2026-04-15 17:00:00',
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $existingAdjustment = SalaryAdjustment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'month' => 4,
            'year' => 2026,
            'type' => 'allowance',
            'label' => 'Phu cap co dinh',
            'amount' => 500000,
        ]);

        $this->actingAs($admin)
            ->post(route('salary.company.lock'), [
                'month' => 4,
                'year' => 2026,
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->from(route('salary.company', ['month' => 4, 'year' => 2026]))
            ->post(route('salary.company.adjustments.store'), [
                'employee_profile_id' => $employee->employeeProfile->id,
                'month' => 4,
                'year' => 2026,
                'type' => 'allowance',
                'label' => 'Phu cap bi chan',
                'amount' => 250000,
            ])
            ->assertRedirect(route('salary.company', ['month' => 4, 'year' => 2026]))
            ->assertSessionHas('warning');

        $this->assertDatabaseMissing('salary_adjustments', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'month' => 4,
            'year' => 2026,
            'label' => 'Phu cap bi chan',
        ]);

        $this->actingAs($admin)
            ->from(route('salary.company', ['month' => 4, 'year' => 2026]))
            ->delete(route('salary.company.adjustments.destroy', $existingAdjustment))
            ->assertRedirect(route('salary.company', ['month' => 4, 'year' => 2026]))
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('salary_adjustments', [
            'id' => $existingAdjustment->id,
            'label' => 'Phu cap co dinh',
            'amount' => 500000,
        ]);
    }

    public function test_salary_statement_separates_allowances_and_manual_deductions(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Adjustment Breakdown');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => '2026-04-15 08:00:00',
            'check_out_at' => '2026-04-15 17:00:00',
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        SalaryAdjustment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'month' => 4,
            'year' => 2026,
            'type' => 'allowance',
            'label' => 'Phu cap xang xe',
            'amount' => 500000,
        ]);

        SalaryAdjustment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'month' => 4,
            'year' => 2026,
            'type' => 'deduction',
            'label' => 'Tam ung',
            'amount' => 100000,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame(500000.0, (float) data_get($page, 'props.summary.allowance_amount'));
        $this->assertSame(100000.0, (float) data_get($page, 'props.summary.manual_deduction_amount'));
        $this->assertSame(
            (float) data_get($page, 'props.summary.attendance_deduction_amount') + 100000.0,
            (float) data_get($page, 'props.summary.deduction_amount')
        );
    }

    public function test_current_month_salary_only_counts_expected_work_days_until_today(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Current Month Window');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:00:00',
            'check_out_at' => '2026-04-14 17:00:00',
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame(10, (int) data_get($page, 'props.summary.expected_work_days'));
        $this->assertSame(19800000.0, (float) data_get($page, 'props.summary.attendance_deduction_amount'));
    }

    public function test_current_month_salary_counts_saturday_when_shift_assignment_requires_it(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Current Month Saturday');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        $shift = WorkShift::query()->create([
            'shift_name' => 'Ca T2-T7',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'handover_break_minutes' => 0,
            'grace_minutes' => 10,
            'late_grace_minutes' => 10,
            'early_leave_grace_minutes' => 5,
            'allows_overtime' => true,
            'is_overnight' => false,
            'is_active' => true,
        ]);

        EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'effective_from' => '2026-04-01',
            'effective_to' => '2026-04-30',
            'weekdays' => [1, 2, 3, 4, 5, 6],
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame(12, (int) data_get($page, 'props.summary.expected_work_days'));
    }

    public function test_salary_statement_handles_open_ended_shift_assignment_without_crashing(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Open Assignment');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        $shift = WorkShift::query()->create([
            'shift_code' => 'SALARYOPEN',
            'shift_name' => 'Ca luong mo',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'handover_break_minutes' => 0,
            'grace_minutes' => 10,
            'late_grace_minutes' => 10,
            'early_leave_grace_minutes' => 5,
            'allows_overtime' => true,
            'is_overnight' => false,
            'is_active' => true,
        ]);

        EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'effective_from' => '2026-04-01',
            'effective_to' => null,
            'weekdays' => [1, 2, 3, 4, 5, 6],
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');
    }

    public function test_recalculate_locked_salary_period_refreshes_snapshot_breakdown_values(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Salary Recalculate');
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Recalculate');
        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => '2026-04-15 08:00:00',
            'check_out_at' => '2026-04-15 17:00:00',
            'worked_minutes' => 480,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('salary.company.lock'), [
                'month' => 4,
                'year' => 2026,
            ])
            ->assertRedirect();

        SalaryAdjustment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'month' => 4,
            'year' => 2026,
            'type' => 'allowance',
            'label' => 'Phu cap ca dem',
            'amount' => 800000,
        ]);

        $this->actingAs($admin)
            ->post(route('salary.company.recalculate'), [
                'month' => 4,
                'year' => 2026,
            ])
            ->assertRedirect();

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame(800000.0, (float) data_get($page, 'props.summary.allowance_amount'));

        $period = PayrollPeriod::query()->where(['month' => 4, 'year' => 2026])->firstOrFail();
        $snapshot = SalarySnapshot::query()->where('payroll_period_id', $period->id)->where('employee_profile_id', $employee->employeeProfile->id)->firstOrFail();
        $this->assertSame(800000.0, (float) data_get($snapshot->payload, 'summary.allowance_amount'));
    }

    public function test_salary_statement_exposes_shift_catalog_data_for_each_record(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Shift Snapshot');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-16',
            'check_in_at' => '2026-04-16 08:00:00',
            'check_out_at' => '2026-04-16 17:30:00',
            'worked_minutes' => 510,
            'attendance_status' => 'on_time',
            'day_status' => 'present',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
            'shift_snapshot' => [
                'shift_name' => 'Ca sang hanh chinh',
                'start_time' => '08:00:00',
                'end_time' => '17:30:00',
                'break_start_time' => '12:00:00',
                'break_end_time' => '13:00:00',
                'standard_minutes' => 510,
                'half_day_minutes' => 255,
                'handover_break_minutes' => 0,
            ],
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $page = $response->viewData('page');

        $this->assertSame('Ca sang hanh chinh', data_get($page, 'props.records.0.shift_name'));
        $this->assertSame('08:00 - 17:30', data_get($page, 'props.records.0.shift_time_range'));
        $this->assertSame(510, (int) data_get($page, 'props.records.0.shift_standard_minutes'));
        $this->assertSame(255, (int) data_get($page, 'props.records.0.shift_half_day_minutes'));
        $this->assertSame(510, (int) data_get($page, 'props.records.0.worked_minutes'));
    }

    public function test_salary_statement_keeps_approved_adjustment_records_approved_after_reconcile(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 20, 10, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Salary Adjustment');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Salary Adjustment');

        $employee->employeeProfile->update([
            'base_salary' => 22000000,
        ]);

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
            'check_in_at' => '2026-04-20 08:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'overtime_minutes' => 0,
            'missing_check_in' => false,
            'missing_check_out' => true,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'missing_check_out',
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $record->id,
                'new_check_out_at' => '2026-04-20 17:52',
                'reason' => 'Bo sung checkout cho bang luong',
            ])
            ->assertSessionHasNoErrors();

        $adjustment = \App\Models\AttendanceAdjustment::query()->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.adjustments.approve', $adjustment), ['note' => 'Duyet de tinh luong'])
            ->assertSessionHasNoErrors();

        AttendanceRecord::query()->whereKey($record->id)->update([
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('salary.mine', [
            'month' => 4,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $record->refresh();
        $page = $response->viewData('page');
        $this->assertSame(1.0, (float) data_get($page, 'props.summary.approved_work_units'));
        $this->assertSame((int) $record->overtime_minutes, (int) data_get($page, 'props.summary.approved_overtime_minutes'));
        $this->assertSame('approved', data_get($page, 'props.records.0.approval_status'));
        $this->assertGreaterThan(0, (float) data_get($page, 'props.summary.base_salary_amount'));
    }

    public function test_hr_can_export_reports_but_employee_cannot(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Five');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Three');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(8),
            'check_out_at' => now()->startOfDay()->setHour(17),
            'worked_minutes' => 540,
            'attendance_status' => 'on_time',
            'is_confirmed' => true,
        ]);

        $this->actingAs($hr)
            ->get(route('attendance.reports.export.excel'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');

        $this->actingAs($hr)
            ->get(route('attendance.reports.export.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($employee)
            ->get(route('attendance.reports.export.excel'))
            ->assertRedirect();
    }

    public function test_mark_absent_command_creates_absent_records_for_missing_attendance(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Absent');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-ABSENT',
            'shift_name' => 'Ca mac dinh',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        $employee->employeeProfile->update([
            'hire_date' => '2026-04-01',
            'default_work_shift_id' => $shift->id,
        ]);

        $this->artisan('attendance:mark-absent', ['date' => '2026-04-15'])
            ->expectsOutput('Marked 1 attendance record(s) as absent.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'attendance_status' => 'absent',
            'approval_status' => 'pending',
            'day_status' => 'absent',
            'is_confirmed' => false,
            'note' => 'Auto marked missing attendance pending review',
        ]);

        $payload = app(\App\Services\AttendanceService::class)->getMyAttendanceData($employee, 4, 2026);
        $this->assertSame(0, data_get($payload, 'summary.unpaid_leave_records'));
        $this->assertSame(1, data_get($payload, 'summary.absent_records'));
        $this->assertSame('absent', data_get($payload, 'records.0.day_status'));
        $this->assertSame('missing_attendance', data_get($payload, 'records.0.violation_status'));
        $this->assertSame('pending', data_get($payload, 'records.0.approval_status'));
    }

    public function test_mark_absent_command_only_marks_scheduled_workdays_from_assignment(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Scheduled Absent');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-A1',
            'shift_name' => 'Ca hanh chinh',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'department_id' => null,
            'work_shift_id' => $shift->id,
            'effective_from' => '2026-04-01',
            'effective_to' => '2026-04-30',
            'weekdays' => [1, 2, 3, 4, 5],
            'is_active' => true,
            'note' => 'Chi lam thu 2 den thu 6',
            'created_by' => $employee->id,
        ]);

        $this->artisan('attendance:mark-absent', ['date' => '2026-04-18'])
            ->assertExitCode(0);

        $this->assertDatabaseMissing('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-18',
        ]);

        $this->artisan('attendance:mark-absent', ['date' => '2026-04-20'])
            ->assertExitCode(0);

        $this->assertDatabaseHas('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
            'approval_status' => 'pending',
            'day_status' => 'absent',
            'is_confirmed' => false,
        ]);
    }

    public function test_employee_can_check_out_overnight_shift_after_midnight_using_open_record_from_previous_day(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Overnight Checkout');

        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-NIGHT',
            'shift_name' => 'Ca dem',
            'start_time' => '22:00:00',
            'end_time' => '06:00:00',
            'break_start_time' => '02:00:00',
            'break_end_time' => '03:00:00',
            'standard_minutes' => 420,
            'half_day_minutes' => 210,
            'is_overnight' => true,
            'allows_overtime' => true,
            'is_active' => true,
        ]);

        $employee->employeeProfile->update([
            'hire_date' => '2026-04-01',
            'default_work_shift_id' => $shift->id,
        ]);

        Carbon::setTestNow(Carbon::create(2026, 4, 15, 22, 5, 0, 'Asia/Ho_Chi_Minh'));

        $this->actingAs($employee)
            ->post(route('attendance.check-in'))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        OvertimeRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'start_at' => '2026-04-16 06:00:00',
            'end_at' => '2026-04-16 07:00:00',
            'requested_minutes' => 60,
            'approved_minutes' => 60,
            'status' => 'approved',
            'reason' => 'Tang ca sau ca dem',
            'requested_by' => $employee->id,
        ]);

        Carbon::setTestNow(Carbon::create(2026, 4, 16, 6, 0, 0, 'Asia/Ho_Chi_Minh'));

        $this->actingAs($employee)
            ->post(route('attendance.check-out'))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $record = AttendanceRecord::query()
            ->where('employee_profile_id', $employee->employeeProfile->id)
            ->whereDate('work_date', '2026-04-15')
            ->firstOrFail();

        $this->assertSame('2026-04-15', optional($record->work_date)->format('Y-m-d'));
        $this->assertNotNull($record->check_out_at);
        $this->assertFalse((bool) $record->missing_check_out);
        $this->assertSame(60, (int) $record->overtime_minutes);
        $this->assertDatabaseMissing('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-16',
        ]);
    }

    public function test_employee_cannot_check_in_when_previous_shift_is_still_open(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Open Prior Shift');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => '2026-04-15 22:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'missing_check_in' => false,
            'missing_check_out' => true,
            'is_confirmed' => false,
        ]);

        Carbon::setTestNow(Carbon::create(2026, 4, 16, 8, 0, 0, 'Asia/Ho_Chi_Minh'));

        $this->actingAs($employee)
            ->post(route('attendance.check-in'))
            ->assertRedirect()
            ->assertSessionHasErrors('error');

        $this->assertSame(1, AttendanceRecord::query()->count());
    }

    public function test_overnight_shift_checkout_uses_record_work_date_for_month_lock_validation(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Overnight Month Lock');

        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-NLOCK',
            'shift_name' => 'Ca dem khoa thang',
            'start_time' => '22:00:00',
            'end_time' => '06:00:00',
            'break_start_time' => '02:00:00',
            'break_end_time' => '03:00:00',
            'standard_minutes' => 420,
            'half_day_minutes' => 210,
            'is_overnight' => true,
            'is_active' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'work_date' => '2026-04-30',
            'check_in_at' => '2026-04-30 22:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'missing_check_in' => false,
            'missing_check_out' => true,
            'is_confirmed' => false,
            'shift_snapshot' => [
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'break_start_time' => '02:00:00',
                'break_end_time' => '03:00:00',
                'standard_minutes' => 420,
                'half_day_minutes' => 210,
                'is_overnight' => true,
            ],
        ]);

        AttendanceMonthLock::query()->create([
            'month' => 5,
            'year' => 2026,
            'department_id' => null,
            'is_locked' => true,
            'locked_by' => $employee->id,
            'locked_at' => '2026-05-01 00:00:00',
            'note' => 'Khoa thang 5',
        ]);

        Carbon::setTestNow(Carbon::create(2026, 5, 1, 6, 0, 0, 'Asia/Ho_Chi_Minh'));

        $this->actingAs($employee)
            ->post(route('attendance.check-out'))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $record = AttendanceRecord::query()->whereDate('work_date', '2026-04-30')->firstOrFail();
        $this->assertNotNull($record->check_out_at);
    }

    public function test_sync_missing_records_service_can_backfill_and_auto_close_overdue_absences(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::create(2026, 4, 24, 9, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Sync Missing');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-SYNC',
            'shift_name' => 'Ca sync cong',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        $employee->employeeProfile->update([
            'hire_date' => '2026-04-01',
            'default_work_shift_id' => $shift->id,
        ]);

        $service = app(\App\Services\AttendanceService::class);
        $createdCount = $service->syncMissingAttendanceRecords('2026-04-21', '2026-04-22');
        $closedCount = $service->closeUnexplainedAbsences(2, '2026-04-24');

        $this->assertSame(2, $createdCount);
        $this->assertSame(2, $closedCount);

        $this->assertDatabaseHas('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-21',
            'attendance_status' => 'absent',
            'approval_status' => 'rejected',
        ]);
        $this->assertDatabaseHas('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-22',
            'attendance_status' => 'absent',
            'approval_status' => 'rejected',
        ]);

        $this->assertSame(2, \App\Models\ApprovalDecisionDelivery::query()->count());
    }

    public function test_report_uses_configured_auto_close_timeout_for_synthetic_absences(): void
    {
        config(['attendance.auto_close_after_days' => 3]);
        Carbon::setTestNow(Carbon::create(2026, 4, 24, 9, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Config Timeout');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Config Timeout');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-CONFIG',
            'shift_name' => 'Ca config timeout',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        $employee->employeeProfile->update([
            'hire_date' => '2026-04-22',
            'default_work_shift_id' => $shift->id,
        ]);

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $statuses = collect($report['records'])
            ->pluck('display_approval_status', 'work_date')
            ->all();

        $this->assertSame('needs_verification', $statuses['2026-04-22'] ?? null);
        $this->assertSame('needs_verification', $statuses['2026-04-23'] ?? null);
    }

    public function test_attendance_payload_starts_from_employee_hire_date(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 26, 9, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Mid Month Hire');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-HIRE',
            'shift_name' => 'Ca mac dinh',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        $employee->employeeProfile->update([
            'hire_date' => '2026-04-21',
            'default_work_shift_id' => $shift->id,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
            'worked_minutes' => 0,
            'attendance_status' => 'absent',
            'approval_status' => 'pending',
            'day_status' => 'absent',
            'is_confirmed' => false,
        ]);

        $payload = app(\App\Services\AttendanceService::class)->getMyAttendanceData($employee, 4, 2026);
        $dates = collect($payload['records'])->pluck('work_date')->all();

        $this->assertNotContains('2026-04-20', $dates);
        $this->assertContains('2026-04-25', $dates);
        $this->assertContains('2026-04-26', $dates);
        $this->assertTrue(collect($dates)->every(fn (string $date) => $date >= '2026-04-21'));
    }

    public function test_mark_absent_command_does_not_mark_before_employee_hire_date(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Future Hire');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-HIRE-ABSENT',
            'shift_name' => 'Ca mac dinh',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        $employee->employeeProfile->update([
            'hire_date' => '2026-04-21',
            'default_work_shift_id' => $shift->id,
        ]);

        $this->artisan('attendance:mark-absent', ['date' => '2026-04-20'])
            ->expectsOutput('Marked 0 attendance record(s) as absent.')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
        ]);
    }

    public function test_close_unexplained_absences_rejects_absent_records_after_two_days_without_request(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 22, 9, 10, 0, 'Asia/Ho_Chi_Minh'));
        Queue::fake();

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Auto Reject Absence');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
            'worked_minutes' => 0,
            'attendance_status' => 'absent',
            'approval_status' => 'pending',
            'day_status' => 'absent',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => false,
            'note' => 'Auto marked missing attendance pending review',
        ]);

        $this->artisan('attendance:close-unexplained-absences', ['--days' => 2, '--as-of' => '2026-04-22'])
            ->expectsOutput('Closed 1 unexplained absence record(s) after 2 day(s).')
            ->assertExitCode(0);

        $record->refresh();

        $this->assertSame('rejected', $record->approval_status);
        $this->assertFalse((bool) $record->is_confirmed);
        $this->assertNotNull($record->rejected_at);
        $this->assertStringContainsString('khong duyet cong sau 2 ngay', $record->approval_note);
        $this->assertSame(1, \App\Models\ApprovalDecisionDelivery::query()->count());
    }

    public function test_close_unexplained_absences_keeps_records_with_pending_request_open(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 22, 9, 10, 0, 'Asia/Ho_Chi_Minh'));
        Queue::fake();

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Pending Absence Request');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-20',
            'worked_minutes' => 0,
            'attendance_status' => 'absent',
            'approval_status' => 'pending',
            'day_status' => 'absent',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => false,
            'note' => 'Auto marked missing attendance pending review',
        ]);

        AttendanceRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'request_type' => 'leave',
            'leave_type' => 'paid',
            'status' => 'pending',
            'from_date' => '2026-04-20',
            'to_date' => '2026-04-20',
            'reason' => 'Nghi dot xuat dang cho duyet',
        ]);

        $this->artisan('attendance:close-unexplained-absences', ['--days' => 2, '--as-of' => '2026-04-22'])
            ->expectsOutput('Closed 0 unexplained absence record(s) after 2 day(s).')
            ->assertExitCode(0);

        $record->refresh();

        $this->assertSame('pending', $record->approval_status);
        $this->assertFalse((bool) $record->is_confirmed);
        $this->assertNull($record->rejected_at);
        Queue::assertNothingPushed();
    }

    public function test_confirming_absent_pending_record_requires_leave_request_instead_of_auto_approving_unpaid_leave(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Confirm Absent');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Confirm Absent');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-21',
            'worked_minutes' => 0,
            'attendance_status' => 'absent',
            'approval_status' => 'pending',
            'day_status' => 'absent',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => false,
            'note' => 'Auto marked missing attendance pending review',
        ]);

        $this->actingAs($hr)
            ->post(route('attendance.confirm', $record), ['note' => 'Chot nghi khong phep'])
            ->assertSessionHasErrors(['error']);

        $record->refresh();

        $this->assertSame('pending', $record->approval_status);
        $this->assertFalse((bool) $record->is_confirmed);
        $this->assertSame('absent', $record->day_status);
    }

    public function test_report_separates_day_type_and_violation_for_missing_checkout_and_marks_needs_verification(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Missing Checkout Report');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Missing Checkout Report');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-MC',
            'shift_name' => 'Ca hanh chinh',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'work_date' => '2026-04-21',
            'check_in_at' => '2026-04-21 08:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'missing_check_in' => false,
            'missing_check_out' => true,
            'is_confirmed' => false,
        ]);

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $this->assertSame('present', data_get($report, 'records.0.day_status'));
        $this->assertSame('missing_check_out', data_get($report, 'records.0.violation_status'));
        $this->assertSame('needs_verification', data_get($report, 'records.0.display_approval_status'));
        $this->assertSame(1, data_get($report, 'summary.violation_records'));
        $this->assertSame(1, data_get($report, 'summary.needs_verification_records'));
        $this->assertSame(0, (int) data_get($report, 'records.0.worked_minutes'));
    }

    public function test_today_open_attendance_before_shift_end_is_not_counted_as_violation(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 21, 16, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Open Shift');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Open Shift');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-OPEN',
            'shift_name' => 'Ca hanh chinh',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'work_date' => '2026-04-21',
            'check_in_at' => '2026-04-21 08:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'missing_check_in' => false,
            'missing_check_out' => true,
            'is_confirmed' => false,
        ]);

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $this->assertSame('present', data_get($report, 'records.0.day_status'));
        $this->assertNull(data_get($report, 'records.0.violation_status'));
        $this->assertSame('pending', data_get($report, 'records.0.display_approval_status'));
        $this->assertSame(0, data_get($report, 'summary.violation_records'));
        $this->assertSame(0, data_get($report, 'summary.needs_verification_records'));
    }

    public function test_zero_late_grace_marks_employee_late_from_first_minute(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Zero Grace');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Zero Grace');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-ZG',
            'shift_name' => 'Ca khong grace',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'grace_minutes' => 0,
            'late_grace_minutes' => 0,
            'early_leave_grace_minutes' => 0,
            'is_active' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'work_date' => '2026-04-21',
            'check_in_at' => '2026-04-21 08:01:00',
            'check_out_at' => '2026-04-21 17:00:00',
            'worked_minutes' => 0,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => false,
        ]);

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $this->assertSame(1, (int) data_get($report, 'records.0.late_minutes'));
        $this->assertSame('late', data_get($report, 'records.0.violation_status'));
    }

    public function test_report_marks_worked_paid_holiday_as_day_fact_instead_of_issue(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Holiday Fact');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Holiday Fact');

        Holiday::query()->create([
            'holiday_date' => '2026-04-21',
            'holiday_name' => 'Ngay le thu nghiem',
            'holiday_type' => 'public',
            'is_paid_leave' => true,
            'is_recurring' => false,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-21',
            'check_in_at' => '2026-04-21 08:00:00',
            'check_out_at' => '2026-04-21 12:00:00',
            'worked_minutes' => 0,
            'attendance_status' => 'on_time',
            'approval_status' => 'approved',
            'day_status' => 'present',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $this->assertSame('holiday_paid', data_get($report, 'records.0.day_status'));
        $this->assertSame('Le co luong (co di lam)', data_get($report, 'records.0.day_status_label'));
        $this->assertTrue((bool) data_get($report, 'records.0.worked_on_special_day'));
        $this->assertNull(data_get($report, 'records.0.violation_status'));
    }

    public function test_report_marks_worked_shift_day_off_as_day_fact_instead_of_issue(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Shift Day Off Fact');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Shift Day Off Fact');
        $shift = WorkShift::query()->create([
            'shift_code' => 'SHIFT-DO',
            'shift_name' => 'Ca nghi thu sau',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        $employee->employeeProfile->update([
            'default_work_shift_id' => $shift->id,
        ]);

        \App\Models\EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'department_id' => null,
            'work_shift_id' => $shift->id,
            'effective_from' => '2026-04-01',
            'effective_to' => '2026-04-30',
            'weekdays' => [1, 2, 3, 4],
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $shift->id,
            'work_date' => '2026-04-17',
            'check_in_at' => '2026-04-17 08:00:00',
            'check_out_at' => '2026-04-17 12:00:00',
            'worked_minutes' => 0,
            'attendance_status' => 'on_time',
            'approval_status' => 'approved',
            'day_status' => 'present',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $this->assertSame('day_off', data_get($report, 'records.0.day_status'));
        $this->assertSame('Nghi theo phan ca (co di lam)', data_get($report, 'records.0.day_status_label'));
        $this->assertTrue((bool) data_get($report, 'records.0.worked_on_special_day'));
        $this->assertNull(data_get($report, 'records.0.violation_status'));
    }

    public function test_employee_can_submit_attendance_request_and_overtime_request(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Request');

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-13',
                'from_time' => '08:00',
                'to_time' => '17:00',
                'reason' => 'Quên check out do mất mạng',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_requests', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'request_type' => 'forgot_check',
            'status' => 'pending',
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'start_at' => '2026-04-15 18:00:00',
                'end_at' => '2026-04-15 20:30:00',
                'reason' => 'Hoàn thành hạng mục gấp',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('overtime_requests', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'status' => 'pending',
            'requested_minutes' => 150,
        ]);
    }

    public function test_employee_can_cancel_pending_attendance_request_and_keep_it_in_history(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Cancel Request');

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-13',
                'from_time' => '08:00',
                'to_time' => '17:00',
                'reason' => 'Quen check out can huy',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $approvalRequest = ApprovalRequest::query()->where('requested_by', $employee->id)->firstOrFail();
        $attendanceRequest = AttendanceRequest::query()->where('approval_request_id', $approvalRequest->id)->firstOrFail();

        $this->actingAs($employee)
            ->delete(route('attendance.requests.destroy', $approvalRequest))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('approval_requests', [
            'id' => $approvalRequest->id,
            'status' => 'cancelled',
            'requested_by' => $employee->id,
            'reviewed_by' => $employee->id,
        ]);

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $attendanceRequest->id,
            'status' => 'cancelled',
        ]);

        $payload = app(\App\Services\AttendanceService::class)->getMyAttendanceData($employee, 4, 2026);
        $historyItem = collect($payload['recent_requests'])->firstWhere('approval_request_id', $approvalRequest->id);

        $this->assertNotNull($historyItem);
        $this->assertSame('cancelled', $historyItem['status']);
    }

    public function test_cancel_pending_leave_request_releases_reserved_leave_balance(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Cancel Leave');
        $leaveType = LeaveType::query()->create([
            'code' => 'CANCEL-AL',
            'name' => 'Annual leave cancel test',
            'is_paid' => true,
            'deducts_balance' => true,
            'annual_quota' => 12,
            'is_active' => true,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'leave',
                'from_date' => '2026-04-15',
                'to_date' => '2026-04-15',
                'leave_type_id' => $leaveType->id,
                'leave_duration_type' => 'full_day',
                'reason' => 'Xin nghi phep roi huy',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $approvalRequest = ApprovalRequest::query()->where('requested_by', $employee->id)->firstOrFail();

        $this->assertDatabaseHas('employee_leave_balances', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'leave_type_id' => $leaveType->id,
            'year' => 2026,
            'pending_days' => 1,
        ]);

        $this->actingAs($employee)
            ->delete(route('attendance.requests.destroy', $approvalRequest))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendance_requests', [
            'approval_request_id' => $approvalRequest->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('employee_leave_balances', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'leave_type_id' => $leaveType->id,
            'year' => 2026,
            'pending_days' => 0,
            'used_days' => 0,
        ]);
    }

    public function test_forgot_check_request_allows_past_date_but_rejects_future_date(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Forgot Check Date Rule');

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-13',
                'from_time' => '08:00',
                'to_time' => '17:00',
                'reason' => 'Quen check out hom qua',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-15',
                'from_time' => '08:00',
                'to_time' => '17:00',
                'reason' => 'Khong duoc phep cho ngay tuong lai',
            ])
            ->assertRedirect(route('attendance.mine'))
            ->assertSessionHasErrors(['request_date']);
    }

    public function test_late_early_request_allows_past_violation_date_but_rejects_future_date(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Late Early Date Rule');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-13',
            'check_in_at' => '2026-04-13 08:00:00',
            'check_out_at' => '2026-04-13 16:30:00',
            'worked_minutes' => 450,
            'late_minutes' => 0,
            'early_leave_minutes' => 30,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'early_leave',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'late_early',
                'request_date' => '2026-04-13',
                'requested_status' => 'early_leave',
                'from_time' => '16:30',
                'to_time' => '17:30',
                'reason' => 'Giai trinh ve som ngay da xay ra',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendance_requests', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'request_type' => 'late_early',
            'request_date' => '2026-04-13',
            'requested_status' => 'early_leave',
            'status' => 'pending',
        ]);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'late_early',
                'request_date' => '2026-04-15',
                'requested_status' => 'late',
                'from_time' => '08:00',
                'to_time' => '08:15',
                'reason' => 'Khong duoc giai trinh ngay tuong lai',
            ])
            ->assertRedirect(route('attendance.mine'))
            ->assertSessionHasErrors(['request_date']);
    }

    public function test_attendance_requests_require_matching_attendance_context(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Strict Request Context');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-13',
            'check_in_at' => '2026-04-13 08:00:00',
            'check_out_at' => '2026-04-13 17:30:00',
            'worked_minutes' => 510,
            'late_minutes' => 0,
            'early_leave_minutes' => 0,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-13',
                'to_time' => '17:30',
                'reason' => 'Ngay da du cham cong',
            ])
            ->assertRedirect(route('attendance.mine'))
            ->assertSessionHasErrors(['request_date']);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-12',
                'to_time' => '17:30',
                'reason' => 'Chua co ban ghi nhung thieu check in',
            ])
            ->assertRedirect(route('attendance.mine'))
            ->assertSessionHasErrors(['from_time']);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-14',
                'from_time' => '08:00',
                'to_time' => '17:30',
                'reason' => 'Khong duoc dien gio checkout tuong lai',
            ])
            ->assertRedirect(route('attendance.mine'))
            ->assertSessionHasErrors(['to_time']);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'late_early',
                'request_date' => '2026-04-13',
                'requested_status' => 'late',
                'from_time' => '08:00',
                'to_time' => '08:15',
                'reason' => 'Ngay khong co vi pham di muon',
            ])
            ->assertRedirect(route('attendance.mine'))
            ->assertSessionHasErrors(['requested_status']);
    }

    public function test_overtime_request_must_follow_catalog_overtime_window(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Catalog OT Window');

        $shift = WorkShift::query()->create([
            'shift_name' => 'Ca co khung tang ca',
            'shift_code' => 'OT-WINDOW',
            'start_time' => '08:00:00',
            'end_time' => '17:30:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 510,
            'half_day_minutes' => 255,
            'allows_overtime' => true,
            'is_active' => true,
        ]);

        $shift->overtimeRule()->create([
            'start_time' => '17:30:00',
            'end_time' => '20:00:00',
            'hourly_rate' => 50000,
        ]);

        $employee->employeeProfile->update([
            'default_work_shift_id' => $shift->id,
        ]);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'start_at' => '2026-04-15 17:00:00',
                'end_at' => '2026-04-15 19:00:00',
                'reason' => 'Tang ca truoc khung cau hinh',
            ])
            ->assertSessionHasErrors(['start_at', 'end_at']);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'start_at' => '2026-04-15 17:30:00',
                'end_at' => '2026-04-15 19:00:00',
                'reason' => 'Tang ca dung khung cau hinh',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('overtime_requests', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'requested_minutes' => 90,
            'status' => 'pending',
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'request_date' => '2026-04-16',
                'reason' => 'Tang ca tu dong theo danh muc',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('overtime_requests', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-16',
            'start_at' => '2026-04-16 17:30:00',
            'end_at' => '2026-04-16 20:00:00',
            'requested_minutes' => 150,
            'status' => 'pending',
        ]);
    }

    public function test_make_up_request_requires_linked_leave_date_and_cannot_exceed_missing_minutes(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Make Up Validation');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-12',
            'worked_minutes' => 240,
            'attendance_status' => 'absent',
            'day_status' => 'leave',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-13',
            'worked_minutes' => 420,
            'attendance_status' => 'late',
            'day_status' => 'late',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'make_up',
                'request_date' => '2026-04-16',
                'from_time' => '18:00',
                'to_time' => '20:00',
                'reason' => 'Thieu ngay nghi can bu',
            ])
            ->assertRedirect(route('attendance.mine'))
            ->assertSessionHasErrors(['make_up_related_leave_date']);

        $this->actingAs($employee)
            ->from(route('attendance.mine'))
            ->post(route('attendance.requests.store'), [
                'request_type' => 'make_up',
                'request_date' => '2026-04-16',
                'from_time' => '18:00',
                'to_time' => '23:00',
                'make_up_related_leave_date' => '2026-04-12',
                'reason' => 'Vuot qua so gio thieu',
            ])
            ->assertRedirect(route('attendance.mine'))
            ->assertSessionHasErrors(['make_up_related_leave_date', 'to_time']);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'make_up',
                'request_date' => '2026-04-16',
                'from_time' => '18:00',
                'to_time' => '22:00',
                'make_up_related_leave_date' => '2026-04-12',
                'reason' => 'Lam bu dung so gio thieu',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendance_requests', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'request_type' => 'make_up',
            'request_date' => '2026-04-16',
            'make_up_related_leave_date' => '2026-04-12',
            'from_time' => '18:00',
            'to_time' => '22:00',
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'make_up',
                'request_date' => '2026-04-17',
                'from_time' => '18:00',
                'to_time' => '19:00',
                'make_up_related_leave_date' => '2026-04-13',
                'reason' => 'Lam bu cho ngay thieu cong',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendance_requests', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'request_type' => 'make_up',
            'request_date' => '2026-04-17',
            'make_up_related_leave_date' => '2026-04-13',
            'from_time' => '18:00',
            'to_time' => '19:00',
        ]);
    }

    public function test_hr_can_approve_attendance_request(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Approval Request');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Approval Request');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-13',
                'from_time' => '08:00',
                'to_time' => '17:30',
                'reason' => 'Quên check out khi mất điện',
            ])
            ->assertRedirect();

        $approvalRequest = ApprovalRequest::query()->where('request_type', 'forgot_check')->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Đã kiểm tra và đồng ý',
            ])
            ->assertRedirect();

        $approvalRequest->refresh();
        $attendanceRequest = AttendanceRequest::query()->firstOrFail();

        $this->assertSame('approved', $approvalRequest->status);
        $this->assertSame($hr->id, $approvalRequest->reviewed_by);
        $this->assertSame('approved', $attendanceRequest->status);
        $this->assertSame($hr->id, $attendanceRequest->applied_by);
        $this->assertDatabaseHas('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-13',
            'approval_status' => 'approved',
            'day_status' => 'present',
        ]);
    }

    public function test_report_payload_counts_approved_leave_records(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Leave Report');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Leave Report');

        $leaveType = \App\Models\LeaveType::query()->create([
            'code' => 'PHEP',
            'name' => 'Nghi phep nam',
            'is_paid' => true,
            'deducts_balance' => false,
            'requires_attachment' => false,
            'is_active' => true,
        ]);

        $approvalRequest = ApprovalRequest::query()->create([
            'request_type' => 'leave',
            'target_type' => AttendanceRequest::class,
            'target_id' => 0,
            'requested_by' => $employee->id,
            'status' => 'pending',
            'reason' => 'Xin nghi phep',
            'submitted_at' => now(),
        ]);

        $attendanceRequest = AttendanceRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'approval_request_id' => $approvalRequest->id,
            'request_type' => 'leave',
            'leave_type_id' => $leaveType->id,
            'leave_type' => 'paid',
            'status' => 'pending',
            'from_date' => '2026-04-15',
            'to_date' => '2026-04-15',
            'reason' => 'Xin nghi phep',
        ]);

        $approvalRequest->update([
            'target_id' => $attendanceRequest->id,
        ]);

        app(\App\Services\AttendanceService::class)->reviewApprovalRequest($approvalRequest, $admin, 'approved', 'Duyet nghi phep');

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $this->assertSame(1, data_get($report, 'summary.leave_records'));
        $this->assertSame('leave', data_get($report, 'records.0.day_status'));
    }

    public function test_report_payload_marks_unapproved_unpaid_leave_as_needs_verification(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Legacy Unpaid Leave');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Legacy Unpaid Leave');

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-21',
            'worked_minutes' => 0,
            'late_minutes' => 0,
            'early_leave_minutes' => 0,
            'overtime_minutes' => 0,
            'attendance_status' => 'absent',
            'approval_status' => 'approved',
            'day_status' => 'unpaid_leave',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
            'note' => 'Legacy auto marked unpaid leave',
        ]);

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $this->assertSame(1, data_get($report, 'summary.unpaid_leave_records'));
        $this->assertSame('unpaid_leave', data_get($report, 'records.0.day_status'));
        $this->assertSame('missing_attendance', data_get($report, 'records.0.violation_status'));
        $this->assertSame('needs_verification', data_get($report, 'records.0.display_approval_status'));
        $this->assertSame('pending', data_get($report, 'records.0.approval_status'));

        $record = AttendanceRecord::query()->where('employee_profile_id', $employee->employeeProfile->id)->firstOrFail();
        $this->assertSame('pending', $record->approval_status);
        $this->assertFalse((bool) $record->is_confirmed);
    }

    public function test_same_authority_level_cannot_approve_attendance_request(): void
    {
        $hrTarget = $this->makeUserWithAuthorityProfile('hr', 'HR Request Target');
        $hrApprover = $this->makeUserWithAuthorityProfile('hr', 'HR Request Approver');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Request Approver');

        $approvalRequest = ApprovalRequest::query()->create([
            'request_type' => 'forgot_check',
            'target_type' => AttendanceRequest::class,
            'target_id' => 0,
            'requested_by' => $hrTarget->id,
            'status' => 'pending',
            'reason' => 'Quen cham cong',
            'submitted_at' => now(),
        ]);

        $attendanceRequest = AttendanceRequest::query()->create([
            'employee_profile_id' => $hrTarget->employeeProfile->id,
            'approval_request_id' => $approvalRequest->id,
            'request_type' => 'forgot_check',
            'status' => 'pending',
            'request_date' => '2026-04-13',
            'from_time' => '08:00',
            'to_time' => '17:00',
            'reason' => 'Quen cham cong',
        ]);

        $approvalRequest->update([
            'target_id' => $attendanceRequest->id,
        ]);

        $this->actingAs($hrApprover)
            ->from(route('attendance.approvals'))
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'HR cung cap duyet',
            ])
            ->assertSessionHasErrors(['error']);

        $approvalRequest->refresh();
        $attendanceRequest->refresh();

        $this->assertSame('pending', $approvalRequest->status);
        $this->assertSame('pending', $attendanceRequest->status);

        $this->actingAs($admin)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Admin cap cao duyet',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $approvalRequest->refresh();
        $attendanceRequest->refresh();

        $this->assertSame('approved', $approvalRequest->status);
        $this->assertSame('approved', $attendanceRequest->status);
    }

    public function test_hr_can_bulk_approve_leave_requests(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Bulk Leave');
        $approver = $this->makeUserWithAuthorityProfile('admin', 'Admin Bulk Leave');

        $leaveType = \App\Models\LeaveType::query()->create([
            'code' => 'PHEP',
            'name' => 'Nghi phep nam',
            'is_paid' => true,
            'deducts_balance' => false,
            'requires_attachment' => false,
            'is_active' => true,
        ]);

        $approvalRequests = collect([22, 23])->map(function (int $day) use ($employee, $leaveType) {
            $approvalRequest = ApprovalRequest::query()->create([
                'request_type' => 'leave',
                'target_type' => AttendanceRequest::class,
                'target_id' => 0,
                'requested_by' => $employee->id,
                'status' => 'pending',
                'reason' => 'Xin nghi phep',
                'submitted_at' => now(),
            ]);

            $attendanceRequest = AttendanceRequest::query()->create([
                'employee_profile_id' => $employee->employeeProfile->id,
                'approval_request_id' => $approvalRequest->id,
                'request_type' => 'leave',
                'leave_type_id' => $leaveType->id,
                'status' => 'pending',
                'from_date' => "2026-04-{$day}",
                'to_date' => "2026-04-{$day}",
                'reason' => 'Xin nghi phep',
            ]);

            $approvalRequest->update([
                'target_id' => $attendanceRequest->id,
            ]);

            return $approvalRequest;
        });

        $this->actingAs($approver)
            ->post(route('leave.request-approvals.approve-bulk'), [
                'approval_request_ids' => $approvalRequests->pluck('id')->all(),
                'note' => 'Duyet nghi phep hang loat',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        foreach ($approvalRequests as $approvalRequest) {
            $approvalRequest->refresh();
            $this->assertSame('approved', $approvalRequest->status);
            $this->assertSame($approver->id, $approvalRequest->reviewed_by);
        }

        $this->assertSame(2, AttendanceRequest::query()->where('status', 'approved')->count());
    }

    public function test_approved_business_trip_request_counts_standard_shift_minutes_without_check_in_out(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Business Trip');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Business Trip');

        $shift = WorkShift::query()->create([
            'shift_code' => 'BT001',
            'shift_name' => 'Ca cong tac',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
            'default_work_shift_id' => $shift->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'business_trip',
                'request_date' => '2026-04-15',
                'business_trip_location' => 'Van phong khach hang quan 1',
                'reason' => 'Gap khach hang ngoai van phong',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $approvalRequest = ApprovalRequest::query()->where('request_type', 'business_trip')->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Duyet cong tac',
            ])
            ->assertRedirect();

        $record = AttendanceRecord::query()
            ->where('employee_profile_id', $employee->employeeProfile->id)
            ->whereDate('work_date', '2026-04-15')
            ->firstOrFail();

        $this->assertSame('business_trip', $record->day_status);
        $this->assertSame('on_time', $record->attendance_status);
        $this->assertSame(480, (int) $record->worked_minutes);
        $this->assertFalse((bool) $record->missing_check_in);
        $this->assertFalse((bool) $record->missing_check_out);
        $this->assertSame('approved', $record->approval_status);
        $this->assertDatabaseHas('attendance_requests', [
            'id' => $approvalRequest->target_id,
            'business_trip_location' => 'Van phong khach hang quan 1',
        ]);
    }

    public function test_approved_attendance_request_uses_assigned_work_shift_for_employee(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Assigned Shift Request');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Assigned Shift Request');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        $assignedShift = WorkShift::query()->create([
            'shift_name' => 'Ca hanh chinh muon',
            'shift_code' => 'CA900',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break_start_time' => '13:00:00',
            'break_end_time' => '14:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $assignedShift->id,
            'effective_from' => '2026-04-01',
            'effective_to' => '2026-04-30',
            'is_active' => true,
            'created_by' => $hr->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-13',
                'from_time' => '09:00',
                'to_time' => '18:00',
                'reason' => 'Quen cham cong',
            ])
            ->assertRedirect();

        $approvalRequest = ApprovalRequest::query()->where('request_type', 'forgot_check')->latest('id')->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Duyet theo ca da gan',
            ])
            ->assertRedirect();

        $record = AttendanceRecord::query()
            ->where('employee_profile_id', $employee->employeeProfile->id)
            ->whereDate('work_date', '2026-04-13')
            ->firstOrFail();

        $this->assertSame($assignedShift->id, (int) $record->work_shift_id);
        $this->assertSame('09:00:00', data_get($record->shift_snapshot, 'start_time'));
        $this->assertSame('18:00:00', data_get($record->shift_snapshot, 'end_time'));
    }

    public function test_hr_can_approve_overtime_request_and_overtime_minutes_are_applied_to_attendance_record(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Overtime Approval');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Overtime Approval');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'start_at' => '2026-04-15 18:00:00',
                'end_at' => '2026-04-15 20:30:00',
                'reason' => 'Hoan thanh hang muc gap',
            ])
            ->assertRedirect();

        $approvalRequest = ApprovalRequest::query()->where('request_type', 'overtime')->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Dong y tang ca',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'overtime_minutes' => 150,
            'approval_status' => 'approved',
        ]);
    }

    public function test_approved_make_up_request_is_preserved_in_report_reconciliation(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Make Up');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Make Up');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Report');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-12',
            'worked_minutes' => 0,
            'attendance_status' => 'absent',
            'day_status' => 'unpaid_leave',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'check_in_at' => Carbon::create(2026, 4, 15, 9, 30, 0, 'Asia/Ho_Chi_Minh'),
            'check_out_at' => Carbon::create(2026, 4, 15, 17, 30, 0, 'Asia/Ho_Chi_Minh'),
            'worked_minutes' => 480,
            'late_minutes' => 90,
            'attendance_status' => 'late',
            'day_status' => 'late',
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'make_up',
                'request_date' => '2026-04-15',
                'from_time' => '18:00',
                'to_time' => '20:00',
                'make_up_related_leave_date' => '2026-04-12',
                'reason' => 'Lam bu da duoc phe duyet',
            ])
            ->assertRedirect();

        $approvalRequest = ApprovalRequest::query()->where('request_type', 'make_up')->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Dong y lam bu',
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->get(route('attendance.reports', ['month' => 4, 'year' => 2026, 'employee_profile_id' => $employee->employeeProfile->id]))
            ->assertOk();

        $record = AttendanceRecord::query()
            ->where('employee_profile_id', $employee->employeeProfile->id)
            ->whereDate('work_date', '2026-04-15')
            ->firstOrFail();

        $this->assertSame('on_time', $record->attendance_status);
        $this->assertSame('present', $record->day_status);
        $this->assertSame(0, (int) $record->late_minutes);
        $this->assertDatabaseHas('attendance_requests', [
            'id' => $approvalRequest->target_id,
            'make_up_related_leave_date' => '2026-04-12',
        ]);
    }

    public function test_business_trip_and_make_up_metadata_are_exposed_in_attendance_pages(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Request Metadata');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Request Metadata');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-12',
            'worked_minutes' => 0,
            'attendance_status' => 'absent',
            'day_status' => 'unpaid_leave',
            'approval_status' => 'approved',
            'missing_check_in' => false,
            'missing_check_out' => false,
            'is_confirmed' => true,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'business_trip',
                'request_date' => '2026-04-15',
                'business_trip_location' => 'Chi nhanh Da Nang',
                'reason' => 'Lam viec voi doi tac',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'make_up',
                'request_date' => '2026-04-16',
                'from_time' => '18:00',
                'to_time' => '20:00',
                'make_up_related_leave_date' => '2026-04-12',
                'reason' => 'Lam bu cuoi tuan',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $myAttendanceData = app(\App\Services\AttendanceService::class)->getMyAttendanceData($employee, 4, 2026);
        $approvalsData = app(\App\Services\AttendanceService::class)->getApprovalsData([
            'month' => 4,
            'year' => 2026,
        ], $hr);

        $businessTripRequest = collect($myAttendanceData['recent_requests'])->firstWhere('request_type', 'business_trip');
        $makeUpRequest = collect($myAttendanceData['recent_requests'])->firstWhere('request_type', 'make_up');
        $makeUpQuota = collect($myAttendanceData['make_up_quota_catalog'])->firstWhere('date', '2026-04-12');
        $approvalBusinessTripRequest = collect($approvalsData['request_approvals'])->firstWhere('request_type', 'business_trip');
        $approvalMakeUpRequest = collect($approvalsData['request_approvals'])->firstWhere('request_type', 'make_up');

        $this->assertSame('Chi nhanh Da Nang', data_get($businessTripRequest, 'business_trip_location'));
        $this->assertSame('2026-04-12', data_get($makeUpRequest, 'make_up_related_leave_date'));
        $this->assertSame(360, data_get($makeUpQuota, 'remaining_minutes'));
        $this->assertSame('Chi nhanh Da Nang', data_get($approvalBusinessTripRequest, 'business_trip_location'));
        $this->assertSame('2026-04-12', data_get($approvalMakeUpRequest, 'make_up_related_leave_date'));
    }

    public function test_paid_leave_reconciliation_keeps_standard_minutes_and_full_work_unit(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Paid Leave Reconcile');
        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Paid Leave Reconcile');

        $leaveType = \App\Models\LeaveType::query()->create([
            'code' => 'PHEP-NAM',
            'name' => 'Nghi phep nam',
            'is_paid' => true,
            'deducts_balance' => false,
            'requires_attachment' => false,
            'is_active' => true,
        ]);

        $approvalRequest = ApprovalRequest::query()->create([
            'request_type' => 'leave',
            'target_type' => AttendanceRequest::class,
            'target_id' => 0,
            'requested_by' => $employee->id,
            'status' => 'pending',
            'reason' => 'Xin nghi phep',
            'submitted_at' => now(),
        ]);

        $attendanceRequest = AttendanceRequest::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'approval_request_id' => $approvalRequest->id,
            'request_type' => 'leave',
            'leave_type_id' => $leaveType->id,
            'leave_type' => 'paid',
            'status' => 'pending',
            'from_date' => '2026-04-15',
            'to_date' => '2026-04-15',
            'reason' => 'Xin nghi phep',
        ]);

        $approvalRequest->update([
            'target_id' => $attendanceRequest->id,
        ]);

        app(\App\Services\AttendanceService::class)->reviewApprovalRequest($approvalRequest, $admin, 'approved', 'Duyet nghi phep');

        $report = app(\App\Services\AttendanceService::class)->buildReportData($admin, [
            'month' => 4,
            'year' => 2026,
            'employee_profile_id' => $employee->employeeProfile->id,
        ]);

        $record = AttendanceRecord::query()
            ->where('employee_profile_id', $employee->employeeProfile->id)
            ->whereDate('work_date', '2026-04-15')
            ->firstOrFail();

        $this->assertSame(480, (int) $record->worked_minutes);
        $this->assertSame(1.0, (float) data_get($report, 'records.0.work_unit'));
        $this->assertSame(480, (int) data_get($report, 'records.0.worked_minutes'));
        $this->assertSame(1.0, (float) data_get($report, 'summary.total_work_units'));
    }

    public function test_approved_overtime_counts_only_minutes_outside_work_shift(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee OT Outside Shift');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR OT Outside Shift');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'start_at' => '2026-04-15 19:00:00',
                'end_at' => '2026-04-15 20:00:00',
                'reason' => 'Lam them buoi chieu',
            ])
            ->assertRedirect();

        $approvalRequest = ApprovalRequest::query()->where('request_type', 'overtime')->latest('id')->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Duyet tang ca',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('overtime_requests', [
            'id' => $approvalRequest->target_id,
            'approved_minutes' => 60,
            'status' => 'approved',
        ]);
    }

    public function test_approved_overtime_uses_employee_assigned_shift_boundaries(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Assigned Shift OT');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Assigned Shift OT');
        $employee->employeeProfile->update([
            'reports_to_user_id' => $hr->id,
        ]);

        $assignedShift = WorkShift::query()->create([
            'shift_name' => 'Ca 10h-19h',
            'shift_code' => 'CA901',
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
            'break_start_time' => '14:00:00',
            'break_end_time' => '15:00:00',
            'standard_minutes' => 480,
            'half_day_minutes' => 240,
            'is_active' => true,
        ]);

        EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_shift_id' => $assignedShift->id,
            'effective_from' => '2026-04-01',
            'effective_to' => '2026-04-30',
            'is_active' => true,
            'created_by' => $hr->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'start_at' => '2026-04-15 19:30:00',
                'end_at' => '2026-04-15 21:00:00',
                'reason' => 'Tang ca theo ca da gan',
            ])
            ->assertRedirect();

        $approvalRequest = ApprovalRequest::query()->where('request_type', 'overtime')->latest('id')->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.request-approvals.approve', $approvalRequest), [
                'note' => 'Duyet tang ca theo ca da gan',
            ])
            ->assertRedirect();

        $record = AttendanceRecord::query()
            ->where('employee_profile_id', $employee->employeeProfile->id)
            ->whereDate('work_date', '2026-04-15')
            ->firstOrFail();

        $this->assertSame($assignedShift->id, (int) $record->work_shift_id);
        $this->assertSame(90, (int) $record->overtime_minutes);
    }

    public function test_adjustment_approvals_hide_self_same_level_and_higher_level_requests(): void
    {
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Reviewer');
        $sameLevel = $this->makeUserWithAuthorityProfile('hr', 'HR Same Level');
        $higherLevel = $this->makeUserWithAuthorityProfile('admin', 'Admin Higher Level');
        $lowerLevel = $this->makeUserWithAuthorityProfile('employee', 'Employee Lower Level');

        $selfRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $hr->employeeProfile->id,
            'work_date' => '2026-04-21',
            'check_in_at' => Carbon::create(2026, 4, 21, 8, 0, 0, 'Asia/Ho_Chi_Minh'),
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        $sameLevelRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $sameLevel->employeeProfile->id,
            'work_date' => '2026-04-21',
            'check_in_at' => Carbon::create(2026, 4, 21, 8, 5, 0, 'Asia/Ho_Chi_Minh'),
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        $higherLevelRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $higherLevel->employeeProfile->id,
            'work_date' => '2026-04-21',
            'check_in_at' => Carbon::create(2026, 4, 21, 8, 10, 0, 'Asia/Ho_Chi_Minh'),
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        $lowerLevelRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $lowerLevel->employeeProfile->id,
            'work_date' => '2026-04-21',
            'check_in_at' => Carbon::create(2026, 4, 21, 8, 15, 0, 'Asia/Ho_Chi_Minh'),
            'approval_status' => 'pending',
            'is_confirmed' => false,
        ]);

        foreach ([
            [$selfRecord, $hr],
            [$sameLevelRecord, $sameLevel],
            [$higherLevelRecord, $higherLevel],
            [$lowerLevelRecord, $lowerLevel],
        ] as [$record, $requester]) {
            $approvalRequest = ApprovalRequest::query()->create([
                'request_type' => 'manual_adjustment',
                'target_type' => AttendanceAdjustment::class,
                'target_id' => 0,
                'requested_by' => $requester->id,
                'status' => 'pending',
                'reason' => 'Xin dieu chinh check-out',
                'submitted_at' => now(),
            ]);

            $adjustment = AttendanceAdjustment::query()->create([
                'attendance_record_id' => $record->id,
                'approval_request_id' => $approvalRequest->id,
                'old_check_in_at' => $record->check_in_at,
                'new_check_in_at' => null,
                'old_check_out_at' => null,
                'new_check_out_at' => Carbon::create(2026, 4, 21, 17, 30, 0, 'Asia/Ho_Chi_Minh'),
                'reason' => 'Xin dieu chinh check-out',
                'status' => 'pending',
                'requested_by' => $requester->id,
            ]);

            $approvalRequest->update([
                'target_id' => $adjustment->id,
            ]);
        }

        $payload = app(\App\Services\AttendanceService::class)->buildAdjustmentApprovalsData([
            'month' => 4,
            'year' => 2026,
        ], $hr);

        $this->assertCount(1, $payload['adjustments']);
        $this->assertSame($lowerLevel->name, data_get($payload, 'adjustments.0.employee_name'));
        $this->assertSame((int) $lowerLevel->id, (int) data_get($payload, 'adjustments.0.employee_user_id'));
    }

    public function test_admin_can_lock_month_and_locked_month_blocks_attendance_changes(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 7, 50, 0, 'Asia/Ho_Chi_Minh'));

        $admin = $this->makeUserWithAuthorityProfile('admin', 'Admin Lock');
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Lock');

        $this->actingAs($admin)
            ->post(route('attendance.month-locks.lock'), [
                'month' => 4,
                'year' => 2026,
                'note' => 'Chốt công tháng 4',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_month_locks', [
            'month' => 4,
            'year' => 2026,
            'is_locked' => 1,
        ]);

        $this->actingAs($employee)
            ->from(route('dashboard'))
            ->post(route('attendance.check-in'))
            ->assertSessionHasErrors(['error']);

        $this->assertDatabaseMissing('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
        ]);

        Carbon::setTestNow();
    }

    public function test_employee_can_submit_checkout_adjustment_for_missing_checkout(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 10, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Adjust');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Adjust Receiver');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'missing_check_in' => false,
            'missing_check_out' => true,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'missing_check_out',
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $record->id,
                'new_check_out_at' => '2026-04-14 17:30',
                'reason' => 'Quen check-out cuoi ngay',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendance_adjustments', [
            'attendance_record_id' => $record->id,
            'status' => 'pending',
            'reason' => 'Quen check-out cuoi ngay',
        ]);

        $this->assertDatabaseHas('approval_requests', [
            'request_type' => 'manual_adjustment',
            'status' => 'pending',
            'requested_by' => $employee->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $hr->id,
            'category' => 'attendance',
            'reference_type' => \App\Models\AttendanceAdjustment::class,
            'reference_id' => \App\Models\AttendanceAdjustment::query()->firstOrFail()->id,
        ]);
    }

    public function test_adjustment_page_hides_approved_attendance_records_from_select_options(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 10, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Adjustment Select');

        $approvedRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:00:00',
            'check_out_at' => '2026-04-14 17:00:00',
            'worked_minutes' => 480,
            'missing_check_in' => false,
            'missing_check_out' => false,
            'attendance_status' => 'on_time',
            'approval_status' => 'approved',
            'day_status' => 'present',
            'is_confirmed' => true,
        ]);

        $pendingRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-13',
            'check_in_at' => '2026-04-13 08:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'missing_check_in' => false,
            'missing_check_out' => true,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'missing_check_out',
            'is_confirmed' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('attendance.adjustments.index'));

        $response->assertOk();
        $response->assertViewHas('page');

        $recordIds = collect(data_get($response->viewData('page'), 'props.records'))->pluck('id')->all();

        $this->assertNotContains($approvedRecord->id, $recordIds);
        $this->assertContains($pendingRecord->id, $recordIds);
    }

    public function test_attendance_adjustment_rejects_unsafe_or_duplicate_payloads(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 10, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Adjust Guard');

        $approvedRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:00:00',
            'check_out_at' => '2026-04-14 17:30:00',
            'worked_minutes' => 570,
            'missing_check_in' => false,
            'missing_check_out' => false,
            'attendance_status' => 'on_time',
            'approval_status' => 'approved',
            'day_status' => 'present',
            'is_confirmed' => true,
        ]);

        $this->actingAs($employee)
            ->from(route('attendance.adjustments.index'))
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $approvedRecord->id,
                'new_check_out_at' => '2026-04-14 18:00',
                'reason' => 'Sua ban ghi da duyet',
            ])
            ->assertSessionHasErrors(['attendance_record_id']);

        $pendingRecord = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-13',
            'check_in_at' => '2026-04-13 09:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'missing_check_in' => false,
            'missing_check_out' => true,
            'attendance_status' => 'late',
            'approval_status' => 'pending',
            'day_status' => 'missing_check_out',
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->from(route('attendance.adjustments.index'))
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $pendingRecord->id,
                'new_check_out_at' => '2026-04-13 08:30',
                'reason' => 'Gio checkout sai thu tu',
            ])
            ->assertSessionHasErrors(['new_check_out_at']);

        $this->actingAs($employee)
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $pendingRecord->id,
                'new_check_out_at' => '2026-04-13 17:30',
                'reason' => 'Gui lan dau',
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($employee)
            ->from(route('attendance.adjustments.index'))
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $pendingRecord->id,
                'new_check_out_at' => '2026-04-13 18:00',
                'reason' => 'Gui trung pending',
            ])
            ->assertSessionHasErrors(['attendance_record_id']);
    }

    public function test_approved_adjustment_merges_new_values_with_request_snapshot(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 10, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Merge');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Merge');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'missing_check_in' => false,
            'missing_check_out' => true,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'missing_check_out',
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $record->id,
                'new_check_out_at' => '2026-04-14 17:30',
                'reason' => 'Bo sung checkout',
            ])
            ->assertSessionHasNoErrors();

        // Simulate the source record changing after submission; approval must still merge
        // from adjustment.old_check_in_at + adjustment.new_check_out_at.
        $record->update([
            'check_in_at' => '2026-04-14 09:30:00',
        ]);

        $adjustment = \App\Models\AttendanceAdjustment::query()->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.adjustments.approve', $adjustment), ['note' => 'Duyet bo sung'])
            ->assertSessionHasNoErrors();

        $record->refresh();

        $this->assertSame('2026-04-14 08:00:00', $record->check_in_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-04-14 17:30:00', $record->check_out_at->format('Y-m-d H:i:s'));
        $this->assertSame(570, (int) $record->worked_minutes);
        $this->assertSame('approved', $record->approval_status);
        $this->assertFalse((bool) $record->missing_check_out);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'category' => 'attendance',
            'reference_type' => \App\Models\AttendanceAdjustment::class,
            'reference_id' => $adjustment->id,
        ]);
    }

    public function test_hr_cannot_manually_resolve_missing_checkout_without_employee_adjustment(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 10, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Strict Missing Checkout');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Strict Missing Checkout');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'missing_check_in' => false,
            'missing_check_out' => true,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'is_confirmed' => false,
        ]);

        $this->actingAs($hr)
            ->post(route('attendance.confirm', $record), [
                'note' => 'Tu nhap gio ra',
                'resolved_check_out_time' => '17:30',
            ])
            ->assertSessionHasErrors(['error']);

        $record->refresh();
        $this->assertNull($record->check_out_at);
        $this->assertSame('pending', $record->approval_status);
        $this->assertFalse((bool) $record->is_confirmed);
    }

    public function test_hr_can_confirm_record_after_employee_adjustment_is_approved(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 10, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Strict Confirm');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Strict Confirm');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:00:00',
            'check_out_at' => null,
            'worked_minutes' => 0,
            'missing_check_in' => false,
            'missing_check_out' => true,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'present',
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $record->id,
                'new_check_out_at' => '2026-04-14 17:30',
                'reason' => 'Bo sung checkout de giai trinh',
            ])
            ->assertSessionHasNoErrors();

        $adjustment = \App\Models\AttendanceAdjustment::query()->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.adjustments.approve', $adjustment), ['note' => 'Duyet dieu chinh'])
            ->assertSessionHasNoErrors();

        $record->refresh();
        $this->assertSame('approved', $record->approval_status);
        $this->assertTrue((bool) $record->is_confirmed);

        $this->actingAs($hr)
            ->post(route('attendance.confirm', $record), ['note' => 'Chot cong sau giai trinh'])
            ->assertSessionHasNoErrors();

        $record->refresh();
        $this->assertSame('approved', $record->approval_status);
        $this->assertTrue((bool) $record->is_confirmed);
    }

    public function test_rejected_adjustment_does_not_confirm_or_change_attendance_record(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 10, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Reject Adjustment');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Reject Adjustment');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-14',
            'check_in_at' => '2026-04-14 08:07:39',
            'check_out_at' => '2026-04-14 12:00:00',
            'worked_minutes' => 232,
            'missing_check_in' => false,
            'missing_check_out' => false,
            'attendance_status' => 'on_time',
            'approval_status' => 'pending',
            'day_status' => 'early_leave',
            'is_confirmed' => false,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.adjustments.store'), [
                'attendance_record_id' => $record->id,
                'new_check_out_at' => '2026-04-14 17:00',
                'reason' => 'Toi bi sai gio',
            ])
            ->assertSessionHasNoErrors();

        $adjustment = \App\Models\AttendanceAdjustment::query()->firstOrFail();

        $this->actingAs($hr)
            ->post(route('attendance.adjustments.reject', $adjustment), ['note' => 'Khong chap nhan'])
            ->assertSessionHasNoErrors();

        $record->refresh();
        $adjustment->refresh();

        $this->assertSame('rejected', $adjustment->status);
        $this->assertSame('2026-04-14 08:07:39', $record->check_in_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-04-14 12:00:00', $record->check_out_at->format('Y-m-d H:i:s'));
        $this->assertSame(232, (int) $record->worked_minutes);
        $this->assertSame('pending', $record->approval_status);
        $this->assertFalse((bool) $record->is_confirmed);
    }

    private function makeUserWithAuthorityProfile(string $profile, string $name): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'is_employee' => 1,
            'status' => 'active',
        ]);

        $position = Position::query()->create([
            'name' => 'Position ' . $name,
            'description' => 'Generated for test',
            'is_active' => true,
            'authority_level' => $profile === 'admin' ? 5 : ($profile === 'hr' ? 4 : 1),
            'capabilities' => $this->capabilitiesForAuthorityProfile($profile),
        ]);
        $position->syncCapabilityCodes($this->capabilitiesForAuthorityProfile($profile));

        EmployeeProfile::query()->create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-' . str_pad((string) $user->id, 3, '0', STR_PAD_LEFT),
            'position_id' => $position->id,
            'hire_date' => now()->toDateString(),
            'employment_status' => 'active',
            'employment_type' => 'official',
            'base_salary' => 10000000,
        ]);

        return $user->fresh('employeeProfile');
    }

    private function capabilitiesForAuthorityProfile(string $profile): array
    {
        if ($profile === 'admin') {
            return Capability::all();
        }

        if ($profile === 'hr') {
            return [
                Capability::VIEW_DASHBOARD,
                Capability::MANAGE_EMPLOYEES,
                Capability::APPROVE_ATTENDANCE,
                Capability::VIEW_ALL_ATTENDANCE,
                Capability::EXPORT_ATTENDANCE,
                Capability::APPROVE_USER_REQUESTS,
                Capability::APPROVE_DEPARTMENT_REQUESTS,
                Capability::APPROVE_SALARY_REQUESTS,
                Capability::VIEW_ALL_PROJECTS,
            ];
        }

        return [
            Capability::VIEW_DASHBOARD,
            Capability::VIEW_OWN_PROFILE,
            Capability::UPDATE_OWN_PROFILE,
            Capability::VIEW_OWN_SALARY,
            Capability::VIEW_OWN_ATTENDANCE,
            Capability::CHECK_IN,
            Capability::CHECK_OUT,
            Capability::REQUEST_ATTENDANCE_ADJUSTMENT,
        ];
    }
}
