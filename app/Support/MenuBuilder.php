<?php

namespace App\Support;

use App\Models\User;

class MenuBuilder
{
    public static function build(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $canViewAllProjects = AccessMatrix::allows($user, 'projects.all.view')
            || $user->hasPositionCapability(PositionCapability::MANAGE_PROJECTS)
            || (bool) ($user->employeeProfile?->is_department_head ?? false);

        $groups = [
            [
                'title' => 'Dashboard',
                'items' => array_values(array_filter([
                    self::item('Dashboard', '/dashboard', 'GridIcon', true),
                    AccessMatrix::allows($user, 'profile.view')
                        ? self::item('Ho so ca nhan', '/my-profile', 'UserCircleIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Co cau to chuc',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'users.view')
                        ? self::item('Nhan su', '/users/employees', 'UserGroupIcon')
                        : null,
                    AccessMatrix::allows($user, 'departments.view')
                        ? self::item('Phong ban', '/departments', 'BuildingIcon')
                        : null,
                    AccessMatrix::allows($user, 'positions.view')
                        ? self::item('Chuc vu', '/positions', 'BriefcaseIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Cham cong',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'attendance.mine.view')
                        ? self::item('Cong cua toi', '/my-attendance', 'ClockIcon')
                        : null,
                    AccessMatrix::allows($user, 'attendance.manage.view')
                        ? self::item('Duyet cong', '/attendance/approvals', 'CheckCircleIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Du an',
                'items' => array_values(array_filter([
                    $canViewAllProjects
                        ? self::item('Danh sach du an', '/projects', 'BoxIcon')
                        : null,
                    !$canViewAllProjects && AccessMatrix::allows($user, 'projects.mine.view')
                        ? self::item('Du an cua toi', '/my-projects', 'BoxIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Bao cao',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'attendance.manage.view')
                        ? self::item('Bao cao cham cong', '/attendance/reports', 'BarChartIcon')
                        : null,
                    $user->hasRole('admin') || $user->hasPositionCapability(PositionCapability::VIEW_ACTIVITY_LOGS)
                        ? self::item('Truy vet hoat dong', '/activity-logs', 'ListCheckIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Cau hinh',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'settings.view')
                        ? self::item('Cau hinh website', '/settings', 'SettingsIcon')
                        : null,
                ])),
            ],
        ];

        return array_values(array_filter($groups, fn (array $group) => !empty($group['items'])));
    }

    private static function item(string $name, string $path, string $icon, bool $exact = false): array
    {
        return [
            'name' => $name,
            'path' => $path,
            'icon' => $icon,
            'exact' => $exact,
        ];
    }
}
