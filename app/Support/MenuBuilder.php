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

        $canViewAllProjects = $can(PositionCapability::VIEW_ALL_PROJECTS);

        $groups = [
            [
                'title' => 'Dashboard',
                'items' => array_values(array_filter([
                    $can(PositionCapability::VIEW_DASHBOARD)
                        ? self::item('Dashboard', '/dashboard', 'GridIcon', true)
                        : null,
                    $can(PositionCapability::VIEW_OWN_PROFILE)
                        ? self::item('Ho so ca nhan', '/my-profile', 'UserCircleIcon')
                        : null,
                    $can(PositionCapability::VIEW_FEEDBACKS)
                        ? self::item('Phan hoi noi bo', '/feedbacks', 'Message2Line')
                        : null,
                    $can(PositionCapability::VIEW_OWN_SALARY)
                        ? self::item('Bang luong ca nhan', '/my-salary', 'MoneyIcon')
                        : null,
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
                    $can(PositionCapability::VIEW_OWN_ATTENDANCE)
                        ? self::item('Cong cua toi', '/my-attendance', 'ClockIcon')
                        : null,
                    $can(PositionCapability::REQUEST_ATTENDANCE_ADJUSTMENT)
                        ? self::item('Dieu chinh cong', '/attendance/adjustments', 'EditIcon')
                        : null,
                    $can(PositionCapability::APPROVE_ATTENDANCE)
                        ? self::item('Duyet cong', '/attendance/approvals', 'CheckCircleIcon')
                        : null,
                    $can(PositionCapability::APPROVE_ATTENDANCE)
                        ? self::item('Duyet dieu chinh cong', '/attendance/adjustments/approvals', 'ListCheckIcon')
                        : null,
                    $can(PositionCapability::APPROVE_ATTENDANCE)
                        ? self::item('Danh muc cham cong', '/attendance/catalogs', 'Calendar2Line')
                        : null,
                ])),
            ],
            [
                'title' => 'Du an',
                'items' => array_values(array_filter([
                    $canViewAllProjects
                        ? self::item('Danh sach du an', '/projects', 'BoxIcon')
                        : null,
                    !$canViewAllProjects && $can(PositionCapability::VIEW_OWN_PROJECTS)
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
