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
                        ? self::item('Hồ sơ cá nhân', '/my-profile', 'UserCircleIcon')
                        : null,
                    $can(PositionCapability::VIEW_FEEDBACKS)
                        ? self::item('Phản hồi nội bộ', '/feedbacks', 'Message2Line', false, [], ['/feedbacks'])
                        : null,
                    $can(PositionCapability::VIEW_OWN_SALARY)
                        ? self::item('Bảng lương cá nhân', '/my-salary', 'MoneyIcon', false, [], ['/my-salary'])
                        : null,
                    $can(PositionCapability::VIEW_ALL_SALARY)
                        ? self::item('Bảng lương công ty', '/salary/company', 'MoneyIcon2', false, [], ['/salary/company'])
                        : null,
                ])),
            ],
            [
                'title' => 'Cơ cấu tổ chức',
                'items' => array_values(array_filter([
                    $can(PositionCapability::MANAGE_EMPLOYEES) || $canAccessUserApprovals
                        ? self::item('Nhân sự', $can(PositionCapability::MANAGE_EMPLOYEES) ? '/users/employees' : '/users/approvals', 'UserGroupIcon', false, [], [
                            '/users/employees',
                            '/users/employee-requests',
                            '/users/approvals',
                        ])
                        : null,
                    $can(PositionCapability::MANAGE_DEPARTMENTS) || $canAccessDepartmentApprovals
                        ? self::item('Phòng ban', $can(PositionCapability::MANAGE_DEPARTMENTS) ? '/departments' : '/departments/approvals', 'BuildingIcon', false, [], [
                            '/departments',
                            '/departments/approvals',
                        ])
                        : null,
                    $can(PositionCapability::MANAGE_POSITIONS)
                        ? self::item('Chức vụ', '/positions', 'BriefcaseIcon', false, ['organization'])
                        : null,
                ])),
            ],
            [
                'title' => 'Chấm công',
                'items' => array_values(array_filter([
                    $can(PositionCapability::VIEW_OWN_ATTENDANCE)
                        ? self::item('Công của tôi', '/my-attendance', 'ClockIcon', false, [], ['/my-attendance'])
                        : null,
                    $can(PositionCapability::VIEW_OWN_ATTENDANCE)
                        ? self::item('Nghỉ phép của tôi', '/my-leave', 'Calendar2Line', false, [], ['/my-leave'])
                        : null,
                    ($can(PositionCapability::APPROVE_ATTENDANCE) || $can(PositionCapability::APPROVE_LEAVE))
                        ? self::item('Duyệt công', '/attendance/approvals', 'CheckCircleIcon', false, [], ['/attendance/approvals'])
                        : null,
                    $can(PositionCapability::APPROVE_ATTENDANCE)
                        ? self::item('Danh mục chấm công', '/attendance/catalogs', 'Calendar2Line')
                        : null,
                    $can(PositionCapability::MANAGE_LEAVE_POLICY)
                        ? self::item('Quản lý nghỉ phép', '/leave-management', 'Calendar2Line', false, [], ['/leave-management'])
                        : null,
                ])),
            ],
            [
                'title' => 'Dự án',
                'items' => array_values(array_filter([
                    $canViewAllProjects
                        ? self::item('Danh sách dự án', '/projects', 'BoxIcon', false, [], ['/projects'])
                        : null,
                    !$canViewAllProjects && $can(PositionCapability::VIEW_OWN_PROJECTS)
                        ? self::item('Dự án của tôi', '/my-projects', 'BoxIcon', false, [], ['/my-projects'])
                        : null,
                ])),
            ],
            [
                'title' => 'Báo cáo',
                'items' => array_values(array_filter([
                    ($can(PositionCapability::VIEW_REPORTS) || $can(PositionCapability::EXPORT_REPORTS))
                        ? self::item('Báo cáo tổng hợp', '/reports', 'PieChartIcon', false, [], ['/reports'])
                        : null,
                    $can(PositionCapability::VIEW_ALL_ATTENDANCE)
                        ? self::item('Báo cáo chấm công', '/attendance/reports', 'BarChartIcon', false, [], ['/attendance/reports'])
                        : null,
                    $can(PositionCapability::VIEW_ACTIVITY_LOGS)
                        ? self::item('Truy vết hoạt động', '/activity-logs', 'ListCheckIcon', false, [], ['/activity-logs'])
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


