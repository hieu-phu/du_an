<?php

namespace App\Support;

use App\Models\User;

class MenuBuilder
{
    public static function build(?User $user, array $notificationCounts = []): array
    {
        if (!$user) {
            return [];
        }

        $can = static fn (string $capability): bool => $user->hasPositionCapability($capability);

        $canViewAllProjects = $can(PositionCapability::VIEW_ALL_PROJECTS);
        $canAccessUserApprovals = $can(PositionCapability::APPROVE_USER_REQUESTS) || $can(PositionCapability::APPROVE_SALARY_REQUESTS);
        $canAccessDepartmentApprovals = $can(PositionCapability::APPROVE_DEPARTMENT_REQUESTS);

        $groups = [
            [
                'title' => 'Dashboard',
                'items' => array_values(array_filter([
                    $can(PositionCapability::VIEW_DASHBOARD)
                        ? self::item('Dashboard', '/dashboard', 'GridIcon', true, ['general', 'system'])
                        : null,
                    $can(PositionCapability::VIEW_OWN_PROFILE)
                        ? self::item('Ho so ca nhan', '/my-profile', 'UserCircleIcon')
                        : null,
                    $can(PositionCapability::VIEW_FEEDBACKS)
                        ? self::item('Phan hoi noi bo', '/feedbacks', 'Message2Line', false, [], ['/feedbacks'])
                        : null,
                    $can(PositionCapability::VIEW_OWN_SALARY)
                        ? self::item('Bang luong ca nhan', '/my-salary', 'MoneyIcon', false, [], ['/my-salary'])
                        : null,
                    $can(PositionCapability::VIEW_ALL_SALARY)
                        ? self::item('Bang luong cong ty', '/salary/company', 'MoneyIcon2', false, [], ['/salary/company'])
                        : null,
                ])),
            ],
            [
                'title' => 'Co cau to chuc',
                'items' => array_values(array_filter([
                    $can(PositionCapability::MANAGE_EMPLOYEES) || $canAccessUserApprovals
                        ? self::item('Nhan su', $can(PositionCapability::MANAGE_EMPLOYEES) ? '/users/employees' : '/users/approvals', 'UserGroupIcon', false, [], [
                            '/users/employees',
                            '/users/employee-requests',
                            '/users/approvals',
                        ])
                        : null,
                    $can(PositionCapability::MANAGE_DEPARTMENTS) || $canAccessDepartmentApprovals
                        ? self::item('Phong ban', $can(PositionCapability::MANAGE_DEPARTMENTS) ? '/departments' : '/departments/approvals', 'BuildingIcon', false, [], [
                            '/departments',
                            '/departments/approvals',
                        ])
                        : null,
                    $can(PositionCapability::MANAGE_POSITIONS)
                        ? self::item('Chuc vu', '/positions', 'BriefcaseIcon', false, ['organization'])
                        : null,
                ])),
            ],
            [
                'title' => 'Cham cong',
                'items' => array_values(array_filter([
                    $can(PositionCapability::VIEW_OWN_ATTENDANCE)
                        ? self::item('Cong cua toi', '/my-attendance', 'ClockIcon', false, [], ['/my-attendance'])
                        : null,
                    $can(PositionCapability::VIEW_OWN_ATTENDANCE)
                        ? self::item('Nghi phep cua toi', '/my-leave', 'Calendar2Line', false, [], ['/my-leave'])
                        : null,
                    ($can(PositionCapability::APPROVE_ATTENDANCE) || $can(PositionCapability::APPROVE_LEAVE))
                        ? self::item('Duyet cong', '/attendance/approvals', 'CheckCircleIcon', false, [], ['/attendance/approvals'])
                        : null,
                    $can(PositionCapability::APPROVE_ATTENDANCE)
                        ? self::item('Danh muc cham cong', '/attendance/catalogs', 'Calendar2Line')
                        : null,
                    $can(PositionCapability::MANAGE_LEAVE_POLICY)
                        ? self::item('Quan ly nghi phep', '/leave-management', 'Calendar2Line', false, [], ['/leave-management'])
                        : null,
                ])),
            ],
            [
                'title' => 'Du an',
                'items' => array_values(array_filter([
                    $canViewAllProjects
                        ? self::item('Danh sach du an', '/projects', 'BoxIcon', false, [], ['/projects'])
                        : null,
                    !$canViewAllProjects && $can(PositionCapability::VIEW_OWN_PROJECTS)
                        ? self::item('Du an cua toi', '/my-projects', 'BoxIcon', false, [], ['/my-projects'])
                        : null,
                ])),
            ],
            [
                'title' => 'Bao cao',
                'items' => array_values(array_filter([
                    ($can(PositionCapability::VIEW_REPORTS) || $can(PositionCapability::EXPORT_REPORTS))
                        ? self::item('Bao cao tong hop', '/reports', 'PieChartIcon', false, [], ['/reports'])
                        : null,
                    $can(PositionCapability::VIEW_ALL_ATTENDANCE)
                        ? self::item('Bao cao cham cong', '/attendance/reports', 'BarChartIcon', false, [], ['/attendance/reports'])
                        : null,
                    $can(PositionCapability::VIEW_ACTIVITY_LOGS)
                        ? self::item('Truy vet hoat dong', '/activity-logs', 'ListCheckIcon', false, [], ['/activity-logs'])
                        : null,
                ])),
            ],
        ];

        return collect($groups)
            ->filter(fn (array $group) => !empty($group['items']))
            ->values()
            ->all();
    }

    private static function item(
        string $name,
        string $path,
        string $icon,
        bool $exact = false,
        array $notificationCategories = [],
        array $notificationPaths = [],
    ): array
    {
        return [
            'name' => $name,
            'path' => $path,
            'icon' => $icon,
            'exact' => $exact,
            'notification_categories' => $notificationCategories,
            'notification_paths' => $notificationPaths,
        ];
    }
}
