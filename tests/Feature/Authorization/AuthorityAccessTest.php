<?php

namespace Tests\Feature\Authorization;

use App\Models\EmployeeProfile;
use App\Models\Notification;
use App\Models\Position;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectDetailComment;
use App\Models\ProjectImplementationDetail;
use App\Models\ProjectMember;
use App\Models\ProjectRole;
use App\Models\User;
use App\Support\MenuBuilder;
use App\Support\PositionCapability as Capability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AuthorityAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_all_protected_pages(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin');

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/users')->assertOk();
        $this->actingAs($admin)->get('/projects')->assertOk();
        $this->actingAs($admin)->get('/attendance/reports')->assertOk();
        $this->actingAs($admin)->get('/departments')->assertOk();
        $this->actingAs($admin)->get('/positions')->assertOk();
    }

    public function test_hr_can_access_hr_pages_but_not_admin_only_pages(): void
    {
        $hr = $this->makeUserWithAuthorityProfile('hr');

        $this->actingAs($hr)->get('/dashboard')->assertOk();
        $this->actingAs($hr)->get('/users')->assertOk();
        $this->actingAs($hr)->get('/projects')->assertOk();
        $this->actingAs($hr)->get('/attendance/reports')->assertOk();
        $this->actingAs($hr)->get('/departments')->assertRedirect();
        $this->actingAs($hr)->get('/positions')->assertRedirect();
    }

    public function test_employee_can_only_access_own_pages(): void
    {
        $employee = $this->makeUserWithAuthorityProfile('employee');

        $this->actingAs($employee)->get('/dashboard')->assertOk();
        $this->actingAs($employee)->get('/my-profile')->assertOk();
        $this->actingAs($employee)->get('/my-attendance')->assertOk();
        $this->actingAs($employee)->get('/my-projects')->assertRedirect();
        $this->actingAs($employee)->get('/users')->assertRedirect();
        $this->actingAs($employee)->get('/projects')->assertForbidden();
        $this->actingAs($employee)->get('/attendance/reports')->assertRedirect();
        $this->actingAs($employee)->get('/departments')->assertRedirect();
        $this->actingAs($employee)->get('/salary/company')->assertRedirect();
        $this->actingAs($employee)->get('/salary/company/export/excel')->assertRedirect();
    }

    public function test_user_with_view_all_salary_capability_can_access_company_salary_page(): void
    {
        $manager = $this->makeUserWithCapabilities([Capability::VIEW_ALL_SALARY]);

        $this->actingAs($manager)->get('/salary/company')->assertOk();
        $this->actingAs($manager)->get('/salary/company/export/excel')->assertOk();
        $this->actingAs($manager)->post('/salary/company/recalculate')->assertRedirect();
        $this->actingAs($manager)->post('/salary/company/adjustments')->assertRedirect();
    }

    public function test_leave_approval_is_merged_into_attendance_approval_menu_and_route(): void
    {
        $leaveApprover = $this->makeUserWithCapabilities([Capability::APPROVE_LEAVE]);

        $menuNames = collect(MenuBuilder::build($leaveApprover))
            ->flatMap(fn (array $group) => $group['items'])
            ->pluck('name')
            ->all();

        $this->assertContains('Duyet cong', $menuNames);
        $this->assertNotContains('Duyet nghi phep', $menuNames);

        $response = $this->actingAs($leaveApprover)->get(route('attendance.approvals'));

        $response->assertOk();
        $response->assertViewHas('page');
        $this->assertSame('leave', data_get($response->viewData('page'), 'props.approval_mode'));

        $this->actingAs($leaveApprover)
            ->get(route('leave.approvals'))
            ->assertRedirect(route('attendance.approvals'));
    }

    public function test_sidebar_groups_show_unread_notification_counts(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin');

        Notification::query()->create([
            'user_id' => $admin->id,
            'title' => 'Cham cong 1',
            'message' => 'Thong bao cham cong chua doc',
            'category' => 'attendance',
            'subdomain' => 'main',
        ]);

        Notification::query()->create([
            'user_id' => $admin->id,
            'title' => 'Cham cong 2',
            'message' => 'Thong bao nghi phep chua doc',
            'category' => 'leave',
            'subdomain' => 'main',
        ]);

        Notification::query()->create([
            'user_id' => $admin->id,
            'title' => 'Phan hoi',
            'message' => 'Thong bao phan hoi chua doc',
            'category' => 'feedback',
            'subdomain' => 'main',
        ]);

        Notification::query()->create([
            'user_id' => $admin->id,
            'title' => 'Da doc',
            'message' => 'Thong bao da doc khong tinh',
            'category' => 'attendance',
            'subdomain' => 'main',
            'read_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('page');

        $menuGroups = collect(data_get($response->viewData('page'), 'props.auth.menuItems', []));

        $this->assertSame(1, (int) data_get($menuGroups->firstWhere('title', 'Dashboard'), 'notification_count'));
        $this->assertSame(2, (int) data_get($menuGroups->firstWhere('title', 'Cham cong'), 'notification_count'));
        $this->assertSame(3, (int) data_get($response->viewData('page'), 'props.auth.notification_counts.all'));
    }

    public function test_hr_cannot_update_admin_account_by_url(): void
    {
        $admin = $this->makeUserWithAuthorityProfile('admin');
        $hr = $this->makeUserWithAuthorityProfile('hr');

        $this->actingAs($hr)
            ->put(route('web.users.account-status', $admin), ['status' => 'blocked'])
            ->assertForbidden();
    }

    public function test_user_with_manage_project_roles_can_manage_project_roles(): void
    {
        $manager = $this->makeUserWithCapabilities([Capability::MANAGE_PROJECT_ROLES]);
        $project = Project::query()->create([
            'name' => 'Internal Project A',
            'start_date' => now()->toDateString(),
            'status' => 'planning',
            'description' => 'Project for authorization testing',
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->post(route('projects.roles.store', $project), [
                'name' => 'Technical Lead',
                'description' => 'Role managed by project manager',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('project_roles', [
            'project_id' => $project->id,
            'name' => 'Technical Lead',
        ]);

        $role = ProjectRole::query()
            ->where('project_id', $project->id)
            ->where('name', 'Technical Lead')
            ->firstOrFail();

        $this->actingAs($manager)
            ->put(route('projects.roles.update', [$project, $role]), [
                'permissions' => ['add_project_member', 'remove_project_member', 'update_implementation_status'],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(
            ['add_project_member', 'remove_project_member', 'update_implementation_status'],
            $role->fresh()->permissions
        );

        $this->actingAs($manager)
            ->delete(route('projects.roles.destroy', [$project, $role]))
            ->assertRedirect();

        $this->assertDatabaseMissing('project_roles', [
            'id' => $role->id,
        ]);
    }

    public function test_project_role_permission_can_manage_members_only_inside_that_project(): void
    {
        $leader = $this->makeUserWithCapabilities([Capability::VIEW_OWN_PROJECTS]);
        $member = $this->makeUserWithCapabilities([Capability::VIEW_OWN_PROJECTS]);
        $outsider = $this->makeUserWithCapabilities([Capability::VIEW_OWN_PROJECTS]);

        $project = Project::query()->create([
            'name' => 'Delegated Project Role',
            'start_date' => now()->toDateString(),
            'status' => 'in_progress',
            'description' => 'Role permission test',
            'created_by' => $leader->id,
            'updated_by' => $leader->id,
        ]);
        $leadRole = ProjectRole::query()->create([
            'project_id' => $project->id,
            'name' => 'Truong nhom du an',
            'permissions' => ['remove_project_member'],
        ]);
        ProjectMember::query()->create([
            'project_id' => $project->id,
            'employee_profile_id' => $leader->employeeProfile->id,
            'project_role_id' => $leadRole->id,
            'joined_at' => now()->toDateString(),
            'is_active' => true,
        ]);
        $projectMember = ProjectMember::query()->create([
            'project_id' => $project->id,
            'employee_profile_id' => $member->employeeProfile->id,
            'joined_at' => now()->toDateString(),
            'is_active' => true,
        ]);

        $this->actingAs($leader)
            ->delete(route('projects.members.destroy', [$project, $projectMember]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('project_members', [
            'id' => $projectMember->id,
            'is_active' => false,
        ]);

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($outsider)
                ->delete(route('projects.members.destroy', [$project, $projectMember]));

            $this->fail('Expected a 403 HttpException for missing project role permission.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_project_manager_can_upload_download_and_delete_project_attachment(): void
    {
        Storage::fake('local');

        $manager = $this->makeUserWithCapabilities([
            Capability::VIEW_ALL_PROJECTS,
            Capability::MANAGE_PROJECTS,
        ]);
        $project = Project::query()->create([
            'name' => 'Attachment Project',
            'start_date' => now()->toDateString(),
            'status' => 'planning',
            'description' => 'Project attachment test',
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->post(route('projects.attachments.store', $project), [
                'files' => [
                    UploadedFile::fake()->create('Tài liệu dự án.pdf', 120, 'application/pdf'),
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Đã tải tệp đính kèm lên dự án.');

        $attachment = ProjectAttachment::query()->firstOrFail();

        $this->assertSame('Tài liệu dự án.pdf', $attachment->original_name);
        Storage::disk('local')->assertExists($attachment->path);

        $this->actingAs($manager)
            ->get(route('projects.attachments.download', [$project, $attachment]))
            ->assertOk();

        $this->actingAs($manager)
            ->delete(route('projects.attachments.destroy', [$project, $attachment]))
            ->assertRedirect()
            ->assertSessionHas('success', 'Đã xóa tệp đính kèm.');

        $this->assertDatabaseMissing('project_attachments', [
            'id' => $attachment->id,
        ]);
    }

    public function test_assigned_employee_can_upload_attachment_to_own_implementation_detail(): void
    {
        Storage::fake('local');

        $employee = $this->makeUserWithCapabilities([
            Capability::VIEW_OWN_PROJECTS,
            Capability::UPDATE_PROJECT_TASK_STATUS,
        ]);
        $project = Project::query()->create([
            'name' => 'Task Attachment Project',
            'start_date' => now()->toDateString(),
            'status' => 'in_progress',
            'description' => 'Task attachment test',
            'created_by' => $employee->id,
            'updated_by' => $employee->id,
        ]);
        ProjectMember::query()->create([
            'project_id' => $project->id,
            'employee_profile_id' => $employee->employeeProfile->id,
            'joined_at' => now()->toDateString(),
            'is_active' => true,
        ]);
        $detail = ProjectImplementationDetail::query()->create([
            'project_id' => $project->id,
            'assigned_to' => $employee->employeeProfile->id,
            'content' => 'Chuẩn bị tài liệu triển khai',
            'execution_date' => now()->toDateString(),
            'duration_days' => 2,
            'expected_end_date' => now()->addDay()->toDateString(),
            'detail_status' => 'in_progress',
            'progress_percent' => 50,
            'created_by' => $employee->id,
            'updated_by' => $employee->id,
        ]);

        $this->actingAs($employee)
            ->post(route('projects.implementation-details.attachments.store', [$project, $detail]), [
                'files' => [
                    UploadedFile::fake()->create('Minh chứng.png', 24, 'image/png'),
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Đã tải tệp đính kèm lên đầu việc.');

        $this->assertDatabaseHas('project_attachments', [
            'project_id' => $project->id,
            'implementation_detail_id' => $detail->id,
            'original_name' => 'Minh chứng.png',
            'uploaded_by' => $employee->id,
        ]);
    }

    public function test_project_progress_and_delay_use_detail_progress_and_recalculated_schedule(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-24 10:00:00'));

        $manager = $this->makeUserWithCapabilities([
            Capability::VIEW_ALL_PROJECTS,
        ]);
        $project = Project::query()->create([
            'name' => 'Progress Calculation Project',
            'start_date' => '2026-04-17',
            'status' => 'in_progress',
            'description' => 'Project progress calculation test',
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);

        ProjectImplementationDetail::query()->create([
            'project_id' => $project->id,
            'content' => 'Lam module database seeder',
            'execution_date' => '2026-04-20',
            'duration_days' => 2,
            'expected_end_date' => '2026-04-30',
            'detail_status' => 'in_progress',
            'progress_percent' => 50,
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);
        ProjectImplementationDetail::query()->create([
            'project_id' => $project->id,
            'content' => 'Cho lam',
            'execution_date' => '2026-04-28',
            'duration_days' => 2,
            'expected_end_date' => '2026-04-29',
            'detail_status' => 'planned',
            'progress_percent' => 90,
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);
        ProjectImplementationDetail::query()->create([
            'project_id' => $project->id,
            'content' => 'Da xong',
            'execution_date' => '2026-04-18',
            'duration_days' => 4,
            'expected_end_date' => '2026-04-21',
            'detail_status' => 'completed',
            'progress_percent' => 10,
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->get(route('projects.index'));

        $response->assertOk();
        $payload = $response->viewData('page');
        $projectPayload = collect(data_get($payload, 'props.projects'))->firstWhere('id', $project->id);
        $details = collect(data_get($projectPayload, 'implementation_details'));

        $this->assertSame(63, (int) data_get($projectPayload, 'progress_percent'));
        $this->assertSame(1, (int) data_get($projectPayload, 'task_summary.completed'));
        $this->assertSame(1, (int) data_get($projectPayload, 'task_summary.delayed'));

        $delayedDetail = $details->firstWhere('content', 'Lam module database seeder');
        $this->assertSame('2026-04-21', data_get($delayedDetail, 'expected_end_date'));
        $this->assertTrue((bool) data_get($delayedDetail, 'is_delayed'));
        $this->assertSame(3, (int) data_get($delayedDetail, 'delay_days'));

        $plannedDetail = $details->firstWhere('content', 'Cho lam');
        $this->assertSame(0, (int) data_get($plannedDetail, 'progress_percent'));

        Carbon::setTestNow();
    }

    public function test_project_member_can_comment_and_delete_own_implementation_detail_comment(): void
    {
        $employee = $this->makeUserWithCapabilities([
            Capability::VIEW_OWN_PROJECTS,
        ]);
        $project = Project::query()->create([
            'name' => 'Task Discussion Project',
            'start_date' => now()->toDateString(),
            'status' => 'in_progress',
            'description' => 'Project detail comment test',
            'created_by' => $employee->id,
            'updated_by' => $employee->id,
        ]);
        ProjectMember::query()->create([
            'project_id' => $project->id,
            'employee_profile_id' => $employee->employeeProfile->id,
            'joined_at' => now()->toDateString(),
            'is_active' => true,
        ]);
        $detail = ProjectImplementationDetail::query()->create([
            'project_id' => $project->id,
            'assigned_to' => $employee->employeeProfile->id,
            'content' => 'Trao doi phuong an thuc hien',
            'execution_date' => now()->toDateString(),
            'duration_days' => 3,
            'expected_end_date' => now()->addDays(2)->toDateString(),
            'detail_status' => 'in_progress',
            'progress_percent' => 25,
            'created_by' => $employee->id,
            'updated_by' => $employee->id,
        ]);

        $this->actingAs($employee)
            ->post(route('projects.implementation-details.comments.store', [$project, $detail]), [
                'content' => 'Da nhan viec va dang xu ly.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Đã gửi bình luận cho đầu việc.');

        $comment = ProjectDetailComment::query()->firstOrFail();

        $this->assertDatabaseHas('project_detail_comments', [
            'project_id' => $project->id,
            'implementation_detail_id' => $detail->id,
            'user_id' => $employee->id,
            'content' => 'Da nhan viec va dang xu ly.',
        ]);

        $this->actingAs($employee)
            ->delete(route('projects.implementation-details.comments.destroy', [$project, $detail, $comment]))
            ->assertRedirect()
            ->assertSessionHas('success', 'Đã xóa bình luận.');

        $this->assertDatabaseMissing('project_detail_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_non_member_cannot_comment_on_implementation_detail(): void
    {
        $member = $this->makeUserWithCapabilities([
            Capability::VIEW_OWN_PROJECTS,
        ]);
        $outsider = $this->makeUserWithCapabilities([
            Capability::VIEW_OWN_PROJECTS,
        ]);
        $project = Project::query()->create([
            'name' => 'Restricted Discussion Project',
            'start_date' => now()->toDateString(),
            'status' => 'in_progress',
            'description' => 'Comment permission test',
            'created_by' => $member->id,
            'updated_by' => $member->id,
        ]);
        ProjectMember::query()->create([
            'project_id' => $project->id,
            'employee_profile_id' => $member->employeeProfile->id,
            'joined_at' => now()->toDateString(),
            'is_active' => true,
        ]);
        $detail = ProjectImplementationDetail::query()->create([
            'project_id' => $project->id,
            'assigned_to' => $member->employeeProfile->id,
            'content' => 'Dau viec noi bo',
            'execution_date' => now()->toDateString(),
            'duration_days' => 2,
            'expected_end_date' => now()->addDay()->toDateString(),
            'detail_status' => 'planned',
            'progress_percent' => 0,
            'created_by' => $member->id,
            'updated_by' => $member->id,
        ]);

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($outsider)
                ->post(route('projects.implementation-details.comments.store', [$project, $detail]), [
                    'content' => 'Toi muon tham gia thao luan.',
                ]);

            $this->fail('Expected a 403 HttpException for non-member comment access.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $this->assertDatabaseCount('project_detail_comments', 0);
    }

    public function test_user_cannot_modify_own_or_higher_position(): void
    {
        $manager = $this->makeUserWithCapabilities([Capability::MANAGE_POSITIONS], 4);
        $ownPositionId = (int) $manager->employeeProfile->position_id;

        $higherPosition = Position::query()->create([
            'name' => 'Higher Position',
            'description' => 'Higher than manager',
            'is_active' => true,
            'authority_level' => 5,
            'capabilities' => [],
        ]);

        $this->actingAs($manager)
            ->put(route('positions.update', $ownPositionId), [
                'name' => 'Renamed Own Position',
                'description' => 'Should be blocked',
                'authority_level' => 4,
                'capabilities' => [Capability::MANAGE_POSITIONS],
                'is_active' => true,
            ])
            ->assertSessionHasErrors(['position']);

        $this->actingAs($manager)
            ->put(route('positions.update', $higherPosition->id), [
                'name' => 'Renamed Higher Position',
                'description' => 'Should be blocked',
                'authority_level' => 5,
                'capabilities' => [],
                'is_active' => true,
            ])
            ->assertSessionHasErrors(['position']);

        $this->actingAs($manager)
            ->put(route('positions.toggle', $ownPositionId), [])
            ->assertSessionHasErrors(['position']);

        $this->actingAs($manager)
            ->delete(route('positions.destroy', $higherPosition->id))
            ->assertSessionHasErrors(['position']);
    }

    private function makeUserWithAuthorityProfile(string $profile): User
    {
        $user = User::factory()->create([
            'is_employee' => 1,
            'status' => 'active',
        ]);

        $position = Position::query()->create([
            'name' => 'Position ' . $profile . ' ' . $user->id,
            'description' => 'Generated for access test',
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

        return $user;
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
                Capability::VIEW_ALL_PROJECTS,
                Capability::VIEW_ALL_ATTENDANCE,
            ];
        }

        return [
            Capability::VIEW_DASHBOARD,
            Capability::VIEW_OWN_PROFILE,
            Capability::UPDATE_OWN_PROFILE,
            Capability::VIEW_OWN_ATTENDANCE,
        ];
    }

    private function makeUserWithCapabilities(array $capabilities, int $authorityLevel = 2): User
    {
        $user = User::factory()->create([
            'is_employee' => 1,
            'status' => 'active',
        ]);

        $position = Position::query()->create([
            'name' => 'Position custom ' . $user->id,
            'description' => 'Generated for custom capability access test',
            'is_active' => true,
            'authority_level' => $authorityLevel,
            'capabilities' => $capabilities,
        ]);
        $position->syncCapabilityCodes($capabilities);

        EmployeeProfile::query()->create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-' . str_pad((string) $user->id, 3, '0', STR_PAD_LEFT),
            'position_id' => $position->id,
            'hire_date' => now()->toDateString(),
            'employment_status' => 'active',
            'employment_type' => 'official',
            'base_salary' => 10000000,
        ]);

        return $user;
    }
}
