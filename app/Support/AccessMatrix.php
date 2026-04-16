<?php

namespace App\Support;

use App\Models\User;

class AccessMatrix
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_HR = 'hr';
    public const ROLE_EMPLOYEE = 'employee';

    private const ABILITIES = [
        'dashboard.view' => [self::ROLE_ADMIN, self::ROLE_HR, self::ROLE_EMPLOYEE],
        'profile.view' => [self::ROLE_ADMIN, self::ROLE_HR, self::ROLE_EMPLOYEE],
        'attendance.mine.view' => [self::ROLE_ADMIN, self::ROLE_HR, self::ROLE_EMPLOYEE],
        'attendance.mine.action' => [self::ROLE_ADMIN, self::ROLE_HR, self::ROLE_EMPLOYEE],
        'attendance.manage.view' => [self::ROLE_ADMIN, self::ROLE_HR],
        'users.view' => [self::ROLE_ADMIN, self::ROLE_HR],
        'users.create' => [self::ROLE_ADMIN, self::ROLE_HR],
        'users.edit' => [self::ROLE_ADMIN, self::ROLE_HR],
        'users.manage_status' => [self::ROLE_ADMIN, self::ROLE_HR],
        'users.approvals.view' => [self::ROLE_ADMIN],
        'users.approvals.review' => [self::ROLE_ADMIN],
        'users.assign_admin' => [self::ROLE_ADMIN],
        'departments.view' => [self::ROLE_ADMIN, self::ROLE_HR],
        'departments.manage' => [self::ROLE_ADMIN, self::ROLE_HR],
        'departments.approvals.view' => [self::ROLE_ADMIN],
        'departments.approvals.review' => [self::ROLE_ADMIN],
        'positions.view' => [self::ROLE_ADMIN],
        'positions.manage' => [self::ROLE_ADMIN],
        'projects.all.view' => [self::ROLE_ADMIN, self::ROLE_HR],
        'projects.mine.view' => [],
        'settings.view' => [self::ROLE_ADMIN],
    ];

    /**
     * Capability map để bật permission UI theo chức vụ.
     */
    private const ABILITY_CAPABILITIES = [
        'attendance.manage.view' => [
            PositionCapability::APPROVE_ATTENDANCE,
            PositionCapability::VIEW_ALL_ATTENDANCE,
            PositionCapability::EXPORT_ATTENDANCE,
        ],
        'users.view' => [
            PositionCapability::MANAGE_EMPLOYEES,
        ],
        'users.create' => [
            PositionCapability::MANAGE_EMPLOYEES,
        ],
        'users.edit' => [
            PositionCapability::MANAGE_EMPLOYEES,
        ],
        'users.manage_status' => [
            PositionCapability::MANAGE_EMPLOYEES,
        ],
        'users.approvals.view' => [
            PositionCapability::APPROVE_REQUESTS,
        ],
        'users.approvals.review' => [
            PositionCapability::APPROVE_REQUESTS,
        ],
        'departments.view' => [
            PositionCapability::MANAGE_DEPARTMENTS,
        ],
        'departments.manage' => [
            PositionCapability::MANAGE_DEPARTMENTS,
        ],
        'departments.approvals.view' => [
            PositionCapability::APPROVE_REQUESTS,
        ],
        'departments.approvals.review' => [
            PositionCapability::APPROVE_REQUESTS,
        ],
        'positions.view' => [
            PositionCapability::MANAGE_POSITIONS,
        ],
        'positions.manage' => [
            PositionCapability::MANAGE_POSITIONS,
        ],
        'projects.all.view' => [
            PositionCapability::VIEW_ALL_PROJECTS,
            PositionCapability::MANAGE_PROJECTS,
            PositionCapability::MANAGE_PROJECT_MEMBERS,
            PositionCapability::MANAGE_PROJECT_ROLES,
        ],
        'projects.mine.view' => [
            PositionCapability::VIEW_ALL_PROJECTS,
            PositionCapability::MANAGE_PROJECTS,
            PositionCapability::MANAGE_PROJECT_MEMBERS,
            PositionCapability::MANAGE_PROJECT_ROLES,
        ],
        'settings.view' => [
            PositionCapability::VIEW_ACTIVITY_LOGS,
        ],
    ];

    public static function roleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_HR => 'HR',
            self::ROLE_EMPLOYEE => 'Nhân viên',
        ];
    }

    public static function allowedRoles(string $ability): array
    {
        return self::ABILITIES[$ability] ?? [];
    }

    public static function allows(?User $user, string $ability): bool
    {
        if (!$user) {
            return false;
        }

        $allowedCapabilities = self::ABILITY_CAPABILITIES[$ability] ?? [];

        if (in_array($ability, ['dashboard.view', 'profile.view', 'attendance.mine.view', 'attendance.mine.action'], true)) {
            return true;
        }

        foreach ($allowedCapabilities as $capability) {
            if ($user->hasPositionCapability($capability)) {
                return true;
            }
        }

        if ($ability === 'projects.mine.view') {
            return $user->hasActiveProjectMembership();
        }

        return false;
    }

    public static function permissionsFor(?User $user): array
    {
        $permissions = [];

        foreach (array_keys(self::ABILITIES) as $ability) {
            $permissions[$ability] = self::allows($user, $ability);
        }

        return $permissions;
    }

    public static function primaryRole(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        foreach ([self::ROLE_ADMIN, self::ROLE_HR, self::ROLE_EMPLOYEE] as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }

        return $user->getRoleNames()->first();
    }
}
