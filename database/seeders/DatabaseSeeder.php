<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Position;
use App\Models\Project;
use App\Models\ProjectImplementationDetail;
use App\Models\ProjectMember;
use App\Models\ProjectRole;
use App\Models\User;
use App\Models\PositionCapability as PositionCapabilityModel;
use App\Support\PositionCapability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private const SYSTEM_OWNER_EMAIL = 'gtvbehieu@gmail.com';
    private const SYSTEM_OWNER_SELF_SERVICE_DENY_REASON = 'Disable self-service attendance, leave, and personal salary for system operator account.';

    public function run(): void
    {
        if (!DB::table('provinces')->exists()) {
            $this->call(VietnamAdministrativeSeeder::class);
        }

        $this->call(PositionCapabilitySeeder::class);

        $fullAccessUser = $this->seedSystemFullAccessUser();
        $hrUser = $this->seedHrManager();
        $employeeUser = $this->seedEmployeeSample();

        $workShiftId = $this->seedWorkShift();
        $this->seedAddressedEmployeeProfile($employeeUser, $workShiftId);
        $this->seedSampleAttendance($employeeUser, $hrUser);
        $this->seedSampleProject($fullAccessUser, $employeeUser);
    }

    private function seedSystemFullAccessUser(): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => self::SYSTEM_OWNER_EMAIL],
            [
                'name' => 'GTV Be Hieu',
                'username' => 'gtvbehieu',
                'password' => Hash::make('password'),
                'status' => 'active',
                'is_employee' => 1,
            ]
        );

        $allCapabilityCodes = $this->allPositionCapabilityCodes();
        $highestAuthorityLevel = $this->resolveHighestAuthorityLevel();

        $position = Position::query()->updateOrCreate(
            ['name' => 'System Full Access'],
            [
                'description' => 'Chuc vu toan quyen cho tai khoan van hanh he thong.',
                'authority_level' => $highestAuthorityLevel,
                'capabilities' => $allCapabilityCodes,
                'is_active' => true,
            ]
        );

        if (!empty($allCapabilityCodes)) {
            $position->capabilitiesCatalog()->sync(
                PositionCapabilityModel::query()
                    ->whereIn('code', $allCapabilityCodes)
                    ->pluck('id')
                    ->all()
            );
        }

        $department = Department::query()->updateOrCreate(
            ['name' => 'Ban dieu hanh he thong'],
            [
                'description' => 'Nhom van hanh va quan tri toan bo he thong.',
                'manager_user_id' => $user->id,
                'is_active' => true,
            ]
        );

        EmployeeProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'employee_code' => 'EMP-GTVBEHIEU',
                'department_id' => $department->id,
                'position_id' => $position->id,
                'hire_date' => '2026-04-17',
                'base_salary' => 0,
                'employment_status' => 'active',
                'employment_type' => 'official',
            ]
        );

        DB::table('employee_profiles')
            ->where('user_id', $user->id)
            ->update([
                'is_department_head' => true,
                'reports_to_user_id' => null,
                'updated_at' => now(),
            ]);

        $this->denySystemOwnerSelfServiceCapabilities($user);

        return $user->fresh('employeeProfile.position');
    }

    private function allPositionCapabilityCodes(): array
    {
        if (!DB::getSchemaBuilder()->hasTable('position_capabilities')) {
            return PositionCapability::all();
        }

        $codes = PositionCapabilityModel::query()
            ->where('is_active', true)
            ->orderBy('module')
            ->orderBy('code')
            ->pluck('code')
            ->all();

        return array_values(array_unique(array_merge(PositionCapability::all(), $codes)));
    }

    private function resolveHighestAuthorityLevel(): int
    {
        if (DB::getSchemaBuilder()->hasTable('authority_levels')) {
            $max = (int) (DB::table('authority_levels')
                ->where('is_active', true)
                ->max('rank') ?? 0);

            if ($max > 0) {
                return $max;
            }
        }

        return 10;
    }

    private function denySystemOwnerSelfServiceCapabilities(User $user): void
    {
        if (!DB::getSchemaBuilder()->hasTable('user_position_capability_overrides')) {
            return;
        }

        $capabilityIds = PositionCapabilityModel::query()
            ->whereIn('code', [
                PositionCapability::CHECK_IN,
                PositionCapability::CHECK_OUT,
                PositionCapability::VIEW_OWN_ATTENDANCE,
                PositionCapability::VIEW_OWN_SALARY,
                PositionCapability::REQUEST_ATTENDANCE_ADJUSTMENT,
            ])
            ->pluck('id');

        $now = now();

        foreach ($capabilityIds as $capabilityId) {
            DB::table('user_position_capability_overrides')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'capability_id' => $capabilityId,
                ],
                [
                    'effect' => 'deny',
                    'reason' => self::SYSTEM_OWNER_SELF_SERVICE_DENY_REASON,
                    'expires_at' => null,
                    'created_by' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function seedHrManager(): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'hr@gmail.com'],
            [
                'name' => 'HR Manager',
                'username' => 'hr_manager',
                'password' => Hash::make('password'),
                'status' => 'active',
                'is_employee' => 1,
            ]
        );

        $position = Position::query()->updateOrCreate(
            ['name' => 'Truong phong HR'],
            [
                'description' => 'Quan ly nhan su va phe duyet van hanh.',
                'authority_level' => 4,
                'capabilities' => [
                    PositionCapability::MANAGE_EMPLOYEES,
                    PositionCapability::APPROVE_ATTENDANCE,
                    PositionCapability::VIEW_ALL_ATTENDANCE,
                    PositionCapability::EXPORT_ATTENDANCE,
                    PositionCapability::APPROVE_REQUESTS,
                    PositionCapability::VIEW_ALL_PROJECTS,
                    PositionCapability::VIEW_FEEDBACKS,
                    PositionCapability::REPLY_FEEDBACK,
                ],
                'is_active' => true,
            ]
        );
        $position->syncCapabilityCodes($position->capabilities ?? []);

        $department = Department::query()->updateOrCreate(
            ['name' => 'Phong nhan su'],
            [
                'description' => 'Quan ly nhan su va cham cong.',
                'manager_user_id' => $user->id,
                'is_active' => true,
            ]
        );

        EmployeeProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'employee_code' => 'EMP-HR-001',
                'department_id' => $department->id,
                'position_id' => $position->id,
                'hire_date' => '2026-04-17',
                'base_salary' => 0,
                'employment_status' => 'active',
                'employment_type' => 'official',
            ]
        );

        DB::table('employee_profiles')
            ->where('user_id', $user->id)
            ->update([
                'is_department_head' => true,
                'updated_at' => now(),
            ]);

        return $user->fresh('employeeProfile.position');
    }

    private function seedEmployeeSample(): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'employee1@gmail.com'],
            [
                'name' => 'Nguyen Van A',
                'username' => 'nv_a',
                'password' => Hash::make('password'),
                'status' => 'active',
                'is_employee' => 1,
            ]
        );

        $position = Position::query()->updateOrCreate(
            ['name' => 'Lap trinh vien'],
            [
                'description' => 'Nhan su tham gia trien khai du an.',
                'authority_level' => 1,
                'capabilities' => [],
                'is_active' => true,
            ]
        );
        $position->syncCapabilityCodes([]);

        $department = Department::query()->updateOrCreate(
            ['name' => 'Phong ky thuat'],
            [
                'description' => 'Phat trien du an va van hanh ky thuat.',
                'manager_user_id' => User::query()->where('email', 'gtvbehieu@gmail.com')->value('id'),
                'is_active' => true,
            ]
        );

        EmployeeProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'employee_code' => 'EMP-001',
                'department_id' => $department->id,
                'position_id' => $position->id,
                'hire_date' => '2023-01-01',
                'base_salary' => 15000000,
                'employment_status' => 'active',
                'employment_type' => 'official',
            ]
        );

        return $user->fresh('employeeProfile.position');
    }

    private function seedWorkShift(): int
    {
        $existingId = DB::table('work_shifts')
            ->where('shift_name', 'Ca hanh chinh')
            ->value('id');

        if ($existingId) {
            DB::table('work_shifts')
                ->where('id', $existingId)
                ->update([
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                    'standard_minutes' => 480,
                    'grace_minutes' => 15,
                    'is_active' => true,
                    'updated_at' => now(),
                ]);

            return (int) $existingId;
        }

        return (int) DB::table('work_shifts')->insertGetId([
            'shift_name' => 'Ca hanh chinh',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'standard_minutes' => 480,
            'grace_minutes' => 15,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedAddressedEmployeeProfile(User $employeeUser, int $workShiftId): void
    {
        $provinceId = DB::table('provinces')->orderBy('id')->value('id');
        $wardId = DB::table('wards')
            ->where('province_id', $provinceId)
            ->orderBy('id')
            ->value('id');

        DB::table('employee_profiles')
            ->where('user_id', $employeeUser->id)
            ->update([
                'default_work_shift_id' => $workShiftId,
                'province_id' => $provinceId,
                'ward_id' => $wardId,
                'address_line' => 'So 1, duong 2',
                'date_of_birth' => '1995-05-10',
                'updated_at' => now(),
            ]);
    }

    private function seedSampleAttendance(User $employeeUser, User $approver): void
    {
        $employeeProfileId = (int) DB::table('employee_profiles')
            ->where('user_id', $employeeUser->id)
            ->value('id');

        if ($employeeProfileId <= 0) {
            return;
        }

        $recordId = DB::table('attendance_records')->updateOrInsert(
            [
                'employee_profile_id' => $employeeProfileId,
                'work_date' => now()->toDateString(),
            ],
            [
                'check_in_at' => now()->copy()->setTime(8, 5, 0),
                'check_out_at' => now()->copy()->setTime(17, 30, 0),
                'worked_minutes' => 480,
                'attendance_status' => 'late',
                'is_confirmed' => true,
                'confirmed_by' => $approver->id,
                'confirmed_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $attendanceRecordId = DB::table('attendance_records')
            ->where('employee_profile_id', $employeeProfileId)
            ->whereDate('work_date', now()->toDateString())
            ->value('id');

        foreach ([
            ['event_type' => 'check_in', 'event_at' => now()->copy()->setTime(8, 5, 0)],
            ['event_type' => 'check_out', 'event_at' => now()->copy()->setTime(17, 30, 0)],
        ] as $event) {
            DB::table('attendance_events')->updateOrInsert(
                [
                    'attendance_record_id' => $attendanceRecordId,
                    'employee_profile_id' => $employeeProfileId,
                    'event_type' => $event['event_type'],
                ],
                [
                    'event_at' => $event['event_at'],
                    'source' => 'seed',
                    'created_by' => $approver->id,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function seedSampleProject(User $owner, User $employeeUser): void
    {
        $project = Project::query()->updateOrCreate(
            [
                'name' => 'He thong HRM Cloud',
                'start_date' => now()->subMonth()->toDateString(),
            ],
            [
                'status' => 'in_progress',
                'description' => 'Du an mau de kiem thu chuc nang HRM.',
                'created_by' => $owner->id,
                'updated_by' => $owner->id,
            ]
        );

        $projectRole = ProjectRole::query()->updateOrCreate(
            [
                'project_id' => $project->id,
                'name' => 'Backend Dev',
            ],
            [
                'description' => 'Phu trach backend cho du an mau.',
            ]
        );

        $employeeProfileId = (int) DB::table('employee_profiles')
            ->where('user_id', $employeeUser->id)
            ->value('id');

        if ($employeeProfileId <= 0) {
            return;
        }

        ProjectMember::query()->updateOrCreate(
            [
                'project_id' => $project->id,
                'employee_profile_id' => $employeeProfileId,
            ],
            [
                'project_role_id' => $projectRole->id,
                'joined_at' => now()->subMonth()->toDateString(),
                'is_active' => true,
            ]
        );

        ProjectImplementationDetail::query()->updateOrCreate(
            [
                'project_id' => $project->id,
                'content' => 'Lam module Database Seeder',
            ],
            [
                'assigned_to' => $employeeProfileId,
                'execution_date' => now()->toDateString(),
                'duration_days' => 2,
                'expected_end_date' => now()->addDays(2)->toDateString(),
                'detail_status' => 'in_progress',
                'created_by' => $owner->id,
                'updated_by' => $owner->id,
            ]
        );
    }
}
