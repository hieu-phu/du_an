<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendanceApproval;
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
use App\Models\Notification;
use App\Models\OvertimeRequest;
use App\Models\SalaryAdjustment;
use App\Models\SalarySnapshot;
use App\Models\User;
use App\Models\WorkShift;
use App\Support\PositionCapability as Capability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 4, 14, 9, 0, 0, 'Asia/Ho_Chi_Minh'));

    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_employee_can_check_in_and_check_out_and_hr_receives_notifications(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 8, 0, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee One');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR One');

        $this->actingAs($employee)
            ->post(route('attendance.check-in'))
            ->assertRedirect();

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
                'standard_minutes' => 465,
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

    public function test_check_out_subtracts_mid_shift_break_and_handover_break_from_worked_minutes(): void
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
            'standard_minutes' => 450,
            'half_day_minutes' => 225,
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

        $this->assertSame(450, (int) $record->worked_minutes);
        $this->assertSame(0, (int) $record->overtime_minutes);
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
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Two');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Two');

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

        foreach ([$employee, $hrTarget, $adminTarget] as $user) {
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

        foreach ([$employee, $hrTarget, $adminTarget] as $user) {
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
                'request_date' => '2026-04-15',
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
        $this->assertStringNotContainsString('HR Hidden Approval', $employeeOptions);
        $this->assertStringNotContainsString('Admin Hidden Approval', $employeeOptions);
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

        $this->artisan('attendance:mark-absent', ['date' => '2026-04-15'])
            ->expectsOutput('Marked 1 attendance record(s) as absent.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('attendance_records', [
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => '2026-04-15',
            'attendance_status' => 'absent',
        ]);
    }

    public function test_employee_can_submit_attendance_request_and_overtime_request(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Request');

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-15',
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

    public function test_hr_can_approve_attendance_request(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Approval Request');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Approval Request');

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'forgot_check',
                'request_date' => '2026-04-15',
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
            'work_date' => '2026-04-15',
            'approval_status' => 'approved',
            'day_status' => 'present',
        ]);
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
            'request_date' => '2026-04-15',
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
            'default_work_shift_id' => $shift->id,
        ]);

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'business_trip',
                'request_date' => '2026-04-15',
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
    }

    public function test_approved_attendance_request_uses_assigned_work_shift_for_employee(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Assigned Shift Request');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Assigned Shift Request');

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
                'request_date' => '2026-04-15',
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
            ->whereDate('work_date', '2026-04-15')
            ->firstOrFail();

        $this->assertSame($assignedShift->id, (int) $record->work_shift_id);
        $this->assertSame('09:00:00', data_get($record->shift_snapshot, 'start_time'));
        $this->assertSame('18:00:00', data_get($record->shift_snapshot, 'end_time'));
    }

    public function test_hr_can_approve_overtime_request_and_overtime_minutes_are_applied_to_attendance_record(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee Overtime Approval');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR Overtime Approval');

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
    }

    public function test_approved_overtime_counts_only_minutes_outside_work_shift(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee', 'Employee OT Outside Shift');
        $hr = $this->makeUserWithAuthorityProfile('hr', 'HR OT Outside Shift');

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
                Capability::APPROVE_REQUESTS,
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
