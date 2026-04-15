<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Vietnam Administrative Units
        $this->call(VietnamAdministrativeSeeder::class);

        // 1. Roles & Permissions (Spatie)
        $roles = [
            'admin' => 'Administrator',
            'hr' => 'Human Resources',
            'employee' => 'Employee'
        ];
        foreach ($roles as $role => $desc) {
            Role::firstOrCreate(['name' => $role], ['description' => $desc]);
        }

        // 2. Default Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'System Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $admin->assignRole('admin');

        $hr = User::firstOrCreate(
            ['email' => 'hr@gmail.com'],
            [
                'name' => 'HR Manager',
                'username' => 'hr_manager',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $hr->assignRole('hr');

        $emp1 = User::firstOrCreate(
            ['email' => 'employee1@gmail.com'],
            [
                'name' => 'Nguyễn Văn A',
                'username' => 'nv_a',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $emp1->assignRole('employee');

        // 3. Departments
        $deptId1 = DB::table('departments')->insertGetId([
            'name' => 'Phòng Nhân Sự',
            'description' => 'Quản lý nhân sự và chấm công',
            'manager_user_id' => $hr->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $deptId2 = DB::table('departments')->insertGetId([
            'name' => 'Phòng Kỹ Thuật',
            'description' => 'Phát triển dự án phần mềm',
            'manager_user_id' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Positions
        $posId1 = DB::table('positions')->insertGetId(['name' => 'Giám đốc', 'created_at' => now(), 'updated_at' => now()]);
        $posId2 = DB::table('positions')->insertGetId(['name' => 'Trưởng phòng HR', 'created_at' => now(), 'updated_at' => now()]);
        $posId3 = DB::table('positions')->insertGetId(['name' => 'Lập trình viên', 'created_at' => now(), 'updated_at' => now()]);

        // 5. Provinces, Districts (Already handled by VietnamAdministrativeSeeder)
        $provId = DB::table('provinces')->where('name', 'LIKE', '%Hà Nội%')->value('id');
        $distId = DB::table('districts')->where('name', 'LIKE', '%Ba Đình%')->value('id');
        $wardId = DB::table('wards')->where('province_id', $provId)->where('name', 'LIKE', '%Hoàn Kiếm%')->value('id');
        
        if (!$provId) {
            $provId = DB::table('provinces')->first()->id ?? 1;
        }

        if (!$distId) {
            // Since districts aren't in the .sql, we manually create one for the sample data
            $distId = DB::table('districts')->insertGetId([
                'province_id' => $provId, 
                'code' => '001', 
                'name' => 'Ba Đình', 
                'type' => 'Quận', 
                'created_at' => now(), 
                'updated_at' => now()
            ]);
        }
        
        if (!$wardId) {
            $wardId = DB::table('wards')->where('province_id', $provId)->value('id');
        }

        // 6. Employee Profiles
        $empProfile1 = DB::table('employee_profiles')->insertGetId([
            'user_id' => $emp1->id,
            'employee_code' => 'EMP-001',
            'department_id' => $deptId2,
            'position_id' => $posId3,
            'province_id' => $provId,
            'district_id' => $distId,
            'ward_id' => $wardId,
            'address_line' => 'Số 1, đường 2',
            'date_of_birth' => '1995-05-10',
            'hire_date' => '2023-01-01',
            'base_salary' => 15000000,
            'employment_status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Work shifts
        DB::table('work_shifts')->insert([
            'shift_name' => 'Ca hành chính',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'standard_minutes' => 480,
            'grace_minutes' => 15,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 8. Attendance Record
        $attRecordId = DB::table('attendance_records')->insertGetId([
            'employee_profile_id' => $empProfile1,
            'work_date' => now()->toDateString(),
            'check_in_at' => now()->copy()->setTime(8, 5, 0),
            'check_out_at' => now()->copy()->setTime(17, 30, 0),
            'worked_minutes' => 480,
            'attendance_status' => 'late',
            'is_confirmed' => true,
            'confirmed_by' => $hr->id,
            'confirmed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('attendance_events')->insert([
            ['attendance_record_id' => $attRecordId, 'employee_profile_id' => $empProfile1, 'event_type' => 'check_in', 'event_at' => now()->copy()->setTime(8, 5, 0), 'created_at' => now(), 'updated_at' => now()],
            ['attendance_record_id' => $attRecordId, 'employee_profile_id' => $empProfile1, 'event_type' => 'check_out', 'event_at' => now()->copy()->setTime(17, 30, 0), 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        // 9. Projects
        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Hệ thống HRM Cloud',
            'start_date' => now()->subMonths(1)->toDateString(),
            'status' => 'in_progress',
            'description' => 'Phát triển dự án quản lý nhân sự',
            'created_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $projectRole1 = DB::table('project_roles')->insertGetId(['project_id' => $projectId, 'name' => 'Backend Dev', 'created_at' => now(), 'updated_at' => now()]);
        
        DB::table('project_members')->insert([
            'project_id' => $projectId,
            'employee_profile_id' => $empProfile1,
            'project_role_id' => $projectRole1,
            'joined_at' => now()->subMonths(1)->toDateString(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('project_implementation_details')->insert([
            'project_id' => $projectId,
            'assigned_to' => $empProfile1,
            'content' => 'Làm module Database Seeder',
            'execution_date' => now()->toDateString(),
            'duration_days' => 2,
            'expected_end_date' => now()->addDays(2)->toDateString(),
            'detail_status' => 'in_progress',
            'created_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
