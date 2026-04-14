<?php

namespace Tests\Feature\Authorization;

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'hr', 'employee'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_admin_can_access_all_protected_pages(): void
    {
        $admin = $this->makeUserWithRole('admin');

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/users')->assertOk();
        $this->actingAs($admin)->get('/projects')->assertOk();
        $this->actingAs($admin)->get('/attendance/reports')->assertOk();
        $this->actingAs($admin)->get('/departments')->assertOk();
        $this->actingAs($admin)->get('/positions')->assertOk();
    }

    public function test_hr_can_access_hr_pages_but_not_admin_only_pages(): void
    {
        $hr = $this->makeUserWithRole('hr');

        $this->actingAs($hr)->get('/dashboard')->assertOk();
        $this->actingAs($hr)->get('/users')->assertOk();
        $this->actingAs($hr)->get('/projects')->assertOk();
        $this->actingAs($hr)->get('/attendance/reports')->assertOk();
        $this->actingAs($hr)->get('/departments')->assertForbidden();
        $this->actingAs($hr)->get('/positions')->assertForbidden();
    }

    public function test_employee_can_only_access_own_pages(): void
    {
        $employee = $this->makeUserWithRole('employee');

        $this->actingAs($employee)->get('/dashboard')->assertOk();
        $this->actingAs($employee)->get('/my-profile')->assertOk();
        $this->actingAs($employee)->get('/my-attendance')->assertOk();
        $this->actingAs($employee)->get('/my-projects')->assertOk();
        $this->actingAs($employee)->get('/users')->assertForbidden();
        $this->actingAs($employee)->get('/projects')->assertForbidden();
        $this->actingAs($employee)->get('/attendance/reports')->assertForbidden();
        $this->actingAs($employee)->get('/departments')->assertForbidden();
    }

    public function test_hr_cannot_update_admin_account_by_url(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $hr = $this->makeUserWithRole('hr');

        $this->actingAs($hr)
            ->put(route('web.users.account-status', $admin), ['status' => 'blocked'])
            ->assertForbidden();
    }

    private function makeUserWithRole(string $role): User
    {
        $user = User::factory()->create([
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

        return $user;
    }
}
