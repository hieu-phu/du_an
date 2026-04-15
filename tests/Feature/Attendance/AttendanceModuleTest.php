<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendanceApproval;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRequest;
use App\Models\AttendanceMonthLock;
use App\Models\ApprovalRequest;
use App\Models\EmployeeProfile;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\OvertimeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'hr', 'employee'] as $role) {
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web'],
                ['description' => strtoupper($role) . ' role']
            );
        }
    }

    public function test_employee_can_check_in_and_check_out_and_hr_receives_notifications(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 7, 55, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithRole('employee', 'Employee One');
        $hr = $this->makeUserWithRole('hr', 'HR One');

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
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 8, 5, 0, 'Asia/Ho_Chi_Minh'));

        $employee = $this->makeUserWithRole('employee', 'Employee Late');

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
        $employee = $this->makeUserWithRole('employee', 'Employee Two');
        $hr = $this->makeUserWithRole('hr', 'HR Two');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $employee->employeeProfile->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(8),
            'check_out_at' => now()->startOfDay()->setHour(17),
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
        $hrTarget = $this->makeUserWithRole('hr', 'HR Target');
        $hrApprover = $this->makeUserWithRole('hr', 'HR Approver');
        $admin = $this->makeUserWithRole('admin', 'Admin One');

        $record = AttendanceRecord::query()->create([
            'employee_profile_id' => $hrTarget->employeeProfile->id,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->startOfDay()->setHour(8),
            'check_out_at' => now()->startOfDay()->setHour(17),
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
        $employee = $this->makeUserWithRole('employee', 'Employee Reject');
        $hr = $this->makeUserWithRole('hr', 'HR Reject');

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
        $employee = $this->makeUserWithRole('employee', 'Employee Three');
        $otherEmployee = $this->makeUserWithRole('employee', 'Employee Four');

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

    public function test_hr_can_export_reports_but_employee_cannot(): void
    {
        $employee = $this->makeUserWithRole('employee', 'Employee Five');
        $hr = $this->makeUserWithRole('hr', 'HR Three');

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
            ->assertForbidden();
    }

    public function test_mark_absent_command_creates_absent_records_for_missing_attendance(): void
    {
        $employee = $this->makeUserWithRole('employee', 'Employee Absent');

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
        $employee = $this->makeUserWithRole('employee', 'Employee Request');

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
        $employee = $this->makeUserWithRole('employee', 'Employee Approval Request');
        $hr = $this->makeUserWithRole('hr', 'HR Approval Request');

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
        $employee = $this->makeUserWithRole('employee', 'Employee Overtime Approval');
        $hr = $this->makeUserWithRole('hr', 'HR Overtime Approval');

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
        $employee = $this->makeUserWithRole('employee', 'Employee Make Up');
        $hr = $this->makeUserWithRole('hr', 'HR Make Up');
        $admin = $this->makeUserWithRole('admin', 'Admin Report');

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

        $this->assertSame('late', $record->attendance_status);
        $this->assertSame('late', $record->day_status);
        $this->assertGreaterThan(0, (int) $record->late_minutes);
    }

    public function test_approved_overtime_counts_only_minutes_outside_work_shift(): void
    {
        $employee = $this->makeUserWithRole('employee', 'Employee OT Outside Shift');
        $hr = $this->makeUserWithRole('hr', 'HR OT Outside Shift');

        $this->actingAs($employee)
            ->post(route('attendance.requests.store'), [
                'request_type' => 'overtime',
                'start_at' => '2026-04-15 13:37:00',
                'end_at' => '2026-04-15 18:00:00',
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

        $admin = $this->makeUserWithRole('admin', 'Admin Lock');
        $employee = $this->makeUserWithRole('employee', 'Employee Lock');

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

    private function makeUserWithRole(string $role, string $name): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'is_employee' => 1,
            'status' => 'active',
        ]);

        $user->assignRole($role);

        EmployeeProfile::query()->create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-' . str_pad((string) $user->id, 3, '0', STR_PAD_LEFT),
            'hire_date' => now()->toDateString(),
            'employment_status' => 'active',
            'employment_type' => 'official',
            'base_salary' => 10000000,
        ]);

        return $user->fresh('employeeProfile');
    }
}
