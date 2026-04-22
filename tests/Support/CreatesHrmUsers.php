<?php

namespace Tests\Support;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Position;
use App\Models\User;

trait CreatesHrmUsers
{
    protected function makeHrmUser(string $name, array $capabilities, int $authorityLevel = 1, array $profileOverrides = []): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'is_employee' => 1,
            'status' => 'active',
        ]);

        $position = Position::query()->create([
            'name' => 'Position ' . $name,
            'description' => 'Generated for HR tests',
            'authority_level' => $authorityLevel,
            'capabilities' => $capabilities,
            'is_active' => true,
        ]);
        $position->syncCapabilityCodes($capabilities);

        $department = Department::query()->create([
            'name' => 'Department ' . $name,
            'description' => 'Generated for HR tests',
            'manager_user_id' => null,
            'is_active' => true,
        ]);

        EmployeeProfile::query()->create(array_merge([
            'user_id' => $user->id,
            'employee_code' => 'EMP-' . str_pad((string) $user->id, 3, '0', STR_PAD_LEFT),
            'department_id' => $department->id,
            'position_id' => $position->id,
            'hire_date' => '2026-01-01',
            'employment_status' => 'active',
            'employment_type' => 'official',
            'base_salary' => 10000000,
        ], $profileOverrides));

        return $user->fresh('employeeProfile.position');
    }
}
