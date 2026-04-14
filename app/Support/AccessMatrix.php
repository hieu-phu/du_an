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
        'projects.mine.view' => [self::ROLE_ADMIN, self::ROLE_HR, self::ROLE_EMPLOYEE],
        'settings.view' => [self::ROLE_ADMIN],
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

        $allowedRoles = self::allowedRoles($ability);

        return !empty($allowedRoles) && $user->hasAnyRole($allowedRoles);
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
