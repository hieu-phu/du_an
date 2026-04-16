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

        $can = static fn (string $capability): bool => $user->hasPositionCapability($capability);

        $canViewAllProjects = $can(PositionCapability::VIEW_ALL_PROJECTS)
            || $can(PositionCapability::MANAGE_PROJECTS)
            || $can(PositionCapability::MANAGE_PROJECT_MEMBERS)
            || $can(PositionCapability::MANAGE_PROJECT_ROLES)
            || (bool) ($user->employeeProfile?->is_department_head ?? false);

        $groups = [
            [
                'title' => 'Dashboard',
                'items' => array_values(array_filter([
                    self::item('Dashboard', '/dashboard', 'GridIcon', true),
                    self::item('Ho so ca nhan', '/my-profile', 'UserCircleIcon'),
                    self::item('Phan hoi noi bo', '/feedbacks', 'Message2Line'),
                ])),
            ],
            [
                'title' => 'Co cau to chuc',
                'items' => array_values(array_filter([
                    $can(PositionCapability::MANAGE_EMPLOYEES)
                        ? self::item('Nhan su', '/users/employees', 'UserGroupIcon')
                        : null,
                    $can(PositionCapability::MANAGE_DEPARTMENTS)
                        ? self::item('Phong ban', '/departments', 'BuildingIcon')
                        : null,
                    $can(PositionCapability::MANAGE_POSITIONS)
                        ? self::item('Chuc vu', '/positions', 'BriefcaseIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Cham cong',
                'items' => array_values(array_filter([
                    self::item('Cong cua toi', '/my-attendance', 'ClockIcon'),
                    $can(PositionCapability::APPROVE_ATTENDANCE)
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
                    !$canViewAllProjects && $user->hasActiveProjectMembership()
                        ? self::item('Du an cua toi', '/my-projects', 'BoxIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Bao cao',
                'items' => array_values(array_filter([
                    ($can(PositionCapability::VIEW_REPORTS) || $can(PositionCapability::EXPORT_REPORTS))
                        ? self::item('Bao cao tong hop', '/reports', 'PieChartIcon')
                        : null,
                    $can(PositionCapability::VIEW_ALL_ATTENDANCE)
                        ? self::item('Bao cao cham cong', '/attendance/reports', 'BarChartIcon')
                        : null,
                    $can(PositionCapability::VIEW_ACTIVITY_LOGS)
                        ? self::item('Truy vet hoat dong', '/activity-logs', 'ListCheckIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Cau hinh',
                'items' => array_values(array_filter([
                    $can(PositionCapability::VIEW_ACTIVITY_LOGS)
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
