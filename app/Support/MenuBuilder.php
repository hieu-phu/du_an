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

        $groups = [
            [
                'title' => 'Dashboard',
                'items' => array_values(array_filter([
                    self::item('Dashboard', '/dashboard', 'GridIcon', true),
                    AccessMatrix::allows($user, 'profile.view')
                        ? self::item('Hồ sơ cá nhân', '/my-profile', 'UserCircleIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Cơ cấu tổ chức',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'users.view')
                        ? self::item('Nhân sự', '/users/employees', 'UserGroupIcon')
                        : null,
                    AccessMatrix::allows($user, 'departments.view')
                        ? self::item('Phòng ban', '/departments', 'BuildingIcon')
                        : null,
                    AccessMatrix::allows($user, 'positions.view')
                        ? self::item('Chức vụ', '/positions', 'BriefcaseIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Chấm công',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'attendance.mine.view')
                        ? self::item('Công của tôi', '/my-attendance', 'ClockIcon')
                        : null,
                    AccessMatrix::allows($user, 'attendance.manage.view')
                        ? self::item('Duyệt công', '/attendance/approvals', 'CheckCircleIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Dự án',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'projects.all.view')
                        ? self::item('Danh sách dự án', '/projects', 'BoxIcon')
                        : null,
                    !AccessMatrix::allows($user, 'projects.all.view') && AccessMatrix::allows($user, 'projects.mine.view')
                        ? self::item('Dự án của tôi', '/my-projects', 'BoxIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Báo cáo',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'attendance.manage.view')
                        ? self::item('Báo cáo chấm công', '/attendance/reports', 'BarChartIcon')
                        : null,
                    $user->hasRole('admin') || $user->hasPositionCapability(PositionCapability::VIEW_ACTIVITY_LOGS)
                        ? self::item('Truy vết hoạt động', '/activity-logs', 'ListCheckIcon')
                        : null,
                ])),
            ],
            [
                'title' => 'Cấu hình',
                'items' => array_values(array_filter([
                    AccessMatrix::allows($user, 'settings.view')
                        ? self::item('Cấu hình website', '/settings', 'SettingsIcon')
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
