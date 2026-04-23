<?php

namespace Tests\Feature\Authorization;

use App\Models\EmployeeProfile;
use App\Models\Notification;
use App\Models\Position;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\User;
use App\Support\MenuBuilder;
use App\Support\PositionCapability as Capability;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->delete(route('projects.roles.destroy', [$project, $role]))
            ->assertRedirect();

        $this->assertDatabaseMissing('project_roles', [
            'id' => $role->id,
        ]);
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
