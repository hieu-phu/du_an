<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\PositionCapability;
use Illuminate\Database\Seeder;

class AuthorityLevelCapabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allCapabilities = PositionCapability::all();
        $capabilitiesByCode = $allCapabilities->pluck('id', 'code')->toArray();

        $mapping = [
            1 => [ // Muc 1 - Nhan vien
                'view_own_profile', 'update_own_profile', 'view_own_salary',
                'check_in', 'check_out', 'view_own_attendance',
                'request_attendance_adjustment', 'request_leave', 'view_own_leave_requests',
                'request_overtime', 'view_own_overtime', 'view_own_projects',
                'update_project_task_status', 'create_feedback', 'view_dashboard'
            ],
            2 => [ // Muc 2 - To pho / Senior
                'view_team_attendance', 'view_team_leave_requests', 
                'view_team_overtime', 'view_team_projects'
            ],
            3 => [ // Muc 3 - Truong nhom
                'approve_team_attendance', 'approve_team_leave', 
                'approve_team_overtime', 'manage_project_members', 
                'assign_project_member', 'remove_project_member'
            ],
            4 => [ // Muc 4 - Truong phong
                'view_department_attendance', 'approve_department_attendance', 
                'view_department_leave_requests', 'approve_department_leave', 
                'view_department_overtime', 'approve_department_overtime', 
                'view_department_projects', 'request_update_employee', 
                'manage_projects', 'view_departments', 'view_positions', 
                'view_work_shifts', 'view_holidays'
            ],
            5 => [ // Muc 5 - Giam doc / Quan ly cao
                'view_all_profiles', 'view_salary_history', 
                'request_salary_change', 'approve_requests', 
                'view_approval_requests', 'view_reports', 
                'view_feedbacks', 'reply_feedback'
            ],
            6 => [ // Muc 6 - Giam doc khoi / VP
                'approve_update_employee', 'approve_salary_change', 
                'manage_leave_policy', 'view_all_attendance', 
                'view_all_leave_requests', 'view_all_overtime', 'view_all_projects',
                'approve_user_requests'
            ],
            7 => [ // Muc 7 - Pho tong giam doc
                'manage_employees', 'manage_salary', 'export_salary', 
                'generate_payroll', 'export_attendance', 'lock_attendance_month', 
                'unlock_attendance_month', 'manage_departments', 'manage_positions', 
                'manage_work_shifts', 'manage_holidays',
                'approve_department_requests', 'approve_salary_requests'
            ],
            8 => [ // Muc 8 - Tong giam doc
                'view_financial_reports', 'sign_documents', 
                'manage_settings', 'export_reports'
            ],
            9 => [ // Muc 9 - Hoi dong quan tri
                // Inheritance handled below
            ],
            10 => [ // Muc 10 - Admin
                // All capabilities handled below
            ],
        ];

        // Cumulative permissions: higher levels get lower levels' permissions
        $cumulativeMapping = [];
        $currentLevelCapabilities = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($i == 10) {
                $cumulativeMapping[$i] = $allCapabilities->pluck('code')->toArray();
            } else {
                if (isset($mapping[$i])) {
                    $currentLevelCapabilities = array_merge($currentLevelCapabilities, $mapping[$i]);
                }
                $cumulativeMapping[$i] = array_unique($currentLevelCapabilities);
            }
        }

        foreach ($cumulativeMapping as $level => $codes) {
            $positions = Position::where('authority_level', $level)->get();
            foreach ($positions as $position) {
                $position->syncCapabilityCodes($codes);
            }
        }

        $this->command?->info('Capabilities synced to positions based on authority levels.');
    }
}
