<?php

namespace App\Support;

use App\Models\Position;
use App\Models\PositionCapability as PositionCapabilityModel;
use Illuminate\Support\Facades\Schema;

class PositionRoleResolver
{
    private static ?array $allowedCapabilityCache = null;

    private const CAPABILITY_MIN_AUTHORITY = [
        PositionCapability::MANAGE_POSITIONS => 5,
        PositionCapability::VIEW_ACTIVITY_LOGS => 5,
        PositionCapability::SIGN_DOCUMENTS => 5,
        PositionCapability::MANAGE_EMPLOYEES => 4,
        PositionCapability::MANAGE_SALARY => 4,
        PositionCapability::VIEW_SALARY => 4,
        PositionCapability::MANAGE_DEPARTMENTS => 4,
        PositionCapability::TRANSFER_EMPLOYEE => 4,
        PositionCapability::APPROVE_ATTENDANCE => 4,
        PositionCapability::APPROVE_LEAVE => 4,
        PositionCapability::APPROVE_REQUESTS => 4,
        PositionCapability::APPROVE_USER_REQUESTS => 4,
        PositionCapability::APPROVE_DEPARTMENT_REQUESTS => 4,
        PositionCapability::APPROVE_SALARY_REQUESTS => 4,
    ];

    public static function normalizeCapabilities(array $capabilities): array
    {
        $allowed = array_fill_keys(self::allowedCapabilities(), true);
        $normalized = [];

        foreach ($capabilities as $capability) {
            if (is_string($capability) && isset($allowed[$capability])) {
                $normalized[$capability] = true;
            }
        }

        return array_values(array_keys($normalized));
    }

    public static function resolveMinimumAuthorityLevelFromCapabilities(array $capabilities): int
    {
        $required = 1;

        foreach (self::normalizeCapabilities($capabilities) as $capability) {
            $required = max($required, (int) (self::CAPABILITY_MIN_AUTHORITY[$capability] ?? 1));
        }

        return $required;
    }

    public static function normalizePositionPayload(array $data): array
    {
        $inputCapabilities = (array) ($data['capabilities'] ?? []);
        $authorityLevel = (int) ($data['authority_level'] ?? 0);

        if ($authorityLevel <= 0) {
            $authorityLevel = 1;
        }

        // If no capabilities provided, use defaults for the level
        if (empty($inputCapabilities)) {
            $inputCapabilities = self::getDefaultCapabilitiesForLevel($authorityLevel);
        }

        $capabilities = self::normalizeCapabilities($inputCapabilities);
        $authorityLevel = max($authorityLevel, self::resolveMinimumAuthorityLevelFromCapabilities($capabilities));

        $data['capabilities'] = $capabilities;
        $data['authority_level'] = $authorityLevel;

        return $data;
    }

    public static function getDefaultCapabilitiesForLevel(int $level): array
    {
        $mapping = [
            1 => [
                'view_own_profile', 'update_own_profile', 'view_own_salary',
                'check_in', 'check_out', 'view_own_attendance',
                'request_attendance_adjustment', 'request_leave', 'view_own_leave_requests',
                'request_overtime', 'view_own_overtime', 'view_own_projects',
                'update_project_task_status', 'create_feedback', 'view_dashboard'
            ],
            2 => [
                'view_team_attendance', 'view_team_leave_requests',
                'view_team_overtime', 'view_team_projects'
            ],
            3 => [
                'approve_team_attendance', 'approve_team_leave',
                'approve_team_overtime', 'manage_project_members',
                'assign_project_member', 'remove_project_member'
            ],
            4 => [
                'view_department_attendance', 'approve_department_attendance',
                'view_department_leave_requests', 'approve_department_leave',
                'view_department_overtime', 'approve_department_overtime',
                'view_department_projects', 'request_update_employee',
                'manage_projects', 'view_departments', 'view_positions',
                'view_work_shifts', 'view_holidays'
            ],
            5 => [
                'view_all_profiles', 'view_salary_history',
                'request_salary_change', 'approve_requests',
                'view_approval_requests', 'view_reports',
                'view_feedbacks', 'reply_feedback'
            ],
            6 => [
                'approve_update_employee', 'approve_salary_change',
                'manage_leave_policy', 'view_all_attendance',
                'view_all_leave_requests', 'view_all_overtime', 'view_all_projects',
                'approve_user_requests'
            ],
            7 => [
                'manage_employees', 'manage_salary', 'export_salary',
                'generate_payroll', 'export_attendance', 'lock_attendance_month',
                'unlock_attendance_month', 'manage_departments', 'manage_positions',
                'manage_work_shifts', 'manage_holidays',
                'approve_department_requests', 'approve_salary_requests'
            ],
            8 => [
                'view_financial_reports', 'sign_documents',
                'manage_settings', 'export_reports'
            ],
        ];

        $all = [];
        if ($level >= 10) {
            return self::allowedCapabilities();
        }

        for ($i = 1; $i <= $level; $i++) {
            if (isset($mapping[$i])) {
                $all = array_merge($all, $mapping[$i]);
            }
        }

        return array_unique($all);
    }

    private static function allowedCapabilities(): array
    {
        if (self::$allowedCapabilityCache !== null) {
            return self::$allowedCapabilityCache;
        }

        $keys = PositionCapability::all();

        if (Schema::hasTable('position_capabilities')) {
            $dbKeys = PositionCapabilityModel::query()->pluck('code')->all();
            $keys = array_values(array_unique(array_merge($keys, $dbKeys)));
        }

        self::$allowedCapabilityCache = $keys;

        return $keys;
    }
}
