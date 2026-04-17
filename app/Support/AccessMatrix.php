<?php

namespace App\Support;

use App\Models\User;

class AccessMatrix
{
    private const ABILITIES = [
        'dashboard.view' => true,
        'profile.view' => true,
        'attendance.mine.view' => true,
        'attendance.mine.action' => true,
        'attendance.manage.view' => true,
        'users.view' => true,
        'users.create' => true,
        'users.edit' => true,
        'users.manage_status' => true,
        'users.approvals.view' => true,
        'users.approvals.review' => true,
        'users.assign_admin' => true,
        'departments.view' => true,
        'departments.manage' => true,
        'departments.approvals.view' => true,
        'departments.approvals.review' => true,
        'positions.view' => true,
        'positions.manage' => true,
        'projects.all.view' => true,
        'projects.mine.view' => true,
        'settings.view' => true,
    ];

    private const ABILITY_CAPABILITIES = [
        'dashboard.view' => [
            PositionCapability::VIEW_DASHBOARD,
        ],
        'profile.view' => [
            PositionCapability::VIEW_OWN_PROFILE,
        ],
        'attendance.mine.view' => [
            PositionCapability::VIEW_OWN_ATTENDANCE,
        ],
        'attendance.mine.action' => [
            PositionCapability::CHECK_IN,
            PositionCapability::CHECK_OUT,
        ],
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
        ],
        'projects.mine.view' => [
            PositionCapability::VIEW_OWN_PROJECTS,
        ],
        'settings.view' => [
            PositionCapability::VIEW_ACTIVITY_LOGS,
        ],
    ];

    public static function allowedRoles(string $ability): array
    {
        return [];
    }

    public static function allows(?User $user, string $ability): bool
    {
        if (!$user) {
            return false;
        }

        $allowedCapabilities = self::ABILITY_CAPABILITIES[$ability] ?? [];

        foreach ($allowedCapabilities as $capability) {
            if ($user->hasPositionCapability($capability)) {
                return true;
            }
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

    public static function canManageAllAttendance(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasAnyPositionCapability([
            PositionCapability::APPROVE_ATTENDANCE,
            PositionCapability::VIEW_ALL_ATTENDANCE,
            PositionCapability::EXPORT_ATTENDANCE,
        ]);
    }

    public static function canApproveRequests(?User $user): bool
    {
        return (bool) ($user?->hasPositionCapability(PositionCapability::APPROVE_REQUESTS));
    }

    public static function canViewAllProjects(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasAnyPositionCapability([
            PositionCapability::VIEW_ALL_PROJECTS,
            PositionCapability::MANAGE_PROJECTS,
            PositionCapability::MANAGE_PROJECT_MEMBERS,
            PositionCapability::MANAGE_PROJECT_ROLES,
        ]);
    }

    public static function canReceiveFeedbackGroup(?User $user, string $group): bool
    {
        if (!$user || !$user->hasPositionCapability(PositionCapability::VIEW_FEEDBACKS)) {
            return false;
        }

        if ($group === 'admin') {
            return $user->hasPositionCapability(PositionCapability::MANAGE_FEEDBACKS);
        }

        if ($group === 'hr') {
            return $user->hasPositionCapability(PositionCapability::REPLY_FEEDBACK)
                || $user->hasPositionCapability(PositionCapability::MANAGE_FEEDBACKS);
        }

        return false;
    }
}
