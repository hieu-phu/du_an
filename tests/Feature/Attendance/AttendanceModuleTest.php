<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendanceApproval;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRequest;
use App\Models\AttendanceMonthLock;
use App\Models\ApprovalRequest;
use App\Models\EmployeeProfile;
use App\Models\Position;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\OvertimeRequest;
use App\Models\User;
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
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 7, 55, 0, 'Asia/Ho_Chi_Minh'));

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
        $this->assertGreaterThan(0, AttendanceEvent::query()->where('attendance_record_id', $record->id)->count());
        $this->assertGreaterThanOrEqual(2, Notification::query()->where('user_id', $hr->id)->count());

        Carbon::setTestNow();
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
            Capability::VIEW_OWN_ATTENDANCE,
            Capability::CHECK_IN,
            Capability::CHECK_OUT,
            Capability::REQUEST_ATTENDANCE_ADJUSTMENT,
        ];
    }
}
