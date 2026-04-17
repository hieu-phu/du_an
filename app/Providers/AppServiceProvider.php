<?php

namespace App\Providers;

use App\Models\PositionCapability as PositionCapabilityModel;
use App\Support\AccessMatrix;
use App\Support\MenuBuilder;
use App\Support\PositionCapability;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Position capability gates.
        foreach ($this->availableCapabilities() as $capability) {
            Gate::define($capability, fn ($user) => $user->hasPositionCapability($capability));
        }

        Inertia::share([
            'auth' => function () {
                $user = auth()->user();
                $availableCapabilities = $this->availableCapabilities();

                return [
                    'user' => $user ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'position_name' => $user->employeeProfile?->position?->name,
                        'authority_level' => (int) ($user->employeeProfile?->position?->authority_level ?? 0),
                        'is_department_head' => (bool) ($user->employeeProfile?->is_department_head ?? false),
                    ] : null,
                    'permissions' => AccessMatrix::permissionsFor($user),
                    'position_capabilities' => $user
                        ? array_fill_keys($availableCapabilities, false) + array_fill_keys(
                            array_filter(
                                $availableCapabilities,
                                fn ($cap) => $user->hasPositionCapability($cap)
                            ),
                            true
                        )
                        : [],
                    'capability_definitions' => [
                        [
                            'group' => 'Nhân sự',
                            'icon' => '👤',
                            'items' => [
                                ['key' => PositionCapability::MANAGE_EMPLOYEES, 'label' => 'Quản lý nhân sự', 'desc' => 'Thêm, sửa, khóa nhân sự'],
                                ['key' => PositionCapability::VIEW_SALARY, 'label' => 'Xem lương nhân viên', 'desc' => 'Xem mức lương của toàn bộ nhân sự'],
                                ['key' => PositionCapability::MANAGE_SALARY, 'label' => 'Điều chỉnh lương', 'desc' => 'Đề xuất và phê duyệt thay đổi lương'],
                            ],
                        ],
                        [
                            'group' => 'Chấm công',
                            'icon' => '🕐',
                            'items' => [
                                ['key' => PositionCapability::APPROVE_ATTENDANCE, 'label' => 'Duyệt chấm công', 'desc' => 'Xác nhận và phê duyệt công của nhân viên'],
                                ['key' => PositionCapability::VIEW_ALL_ATTENDANCE, 'label' => 'Xem toàn bộ chấm công', 'desc' => 'Xem báo cáo công của tất cả nhân sự'],
                                ['key' => PositionCapability::EXPORT_ATTENDANCE, 'label' => 'Xuất báo cáo chấm công', 'desc' => 'Xuất Excel / PDF'],
                            ],
                        ],
                        [
                            'group' => 'Nghỉ phép',
                            'icon' => '📅',
                            'items' => [
                                ['key' => PositionCapability::APPROVE_LEAVE, 'label' => 'Phê duyệt ngày nghỉ', 'desc' => 'Duyệt hoặc từ chối đơn xin nghỉ'],
                                ['key' => PositionCapability::MANAGE_LEAVE_POLICY, 'label' => 'Quản lý chính sách nghỉ', 'desc' => 'Thiết lập quy định nghỉ phép'],
                            ],
                        ],
                        [
                            'group' => 'Dự án',
                            'icon' => '📁',
                            'items' => [
                                ['key' => PositionCapability::MANAGE_PROJECTS, 'label' => 'Quản lý dự án', 'desc' => 'Tạo, sửa, phân công dự án'],
                                ['key' => PositionCapability::MANAGE_PROJECT_MEMBERS, 'label' => 'Quản lý thành viên dự án', 'desc' => 'Thêm/xóa thành viên'],
                                ['key' => PositionCapability::MANAGE_PROJECT_ROLES, 'label' => 'Quản lý vai trò dự án', 'desc' => 'Tạo và xóa vai trò trong từng dự án'],
                                ['key' => PositionCapability::VIEW_ALL_PROJECTS, 'label' => 'Xem toàn bộ dự án', 'desc' => 'Xem tất cả dự án trong hệ thống'],
                            ],
                        ],
                        [
                            'group' => 'Phòng ban & Tổ chức',
                            'icon' => '🏢',
                            'items' => [
                                ['key' => PositionCapability::MANAGE_DEPARTMENTS, 'label' => 'Quản lý phòng ban', 'desc' => 'Thêm, sửa, khóa phòng ban'],
                                ['key' => PositionCapability::MANAGE_POSITIONS, 'label' => 'Quản lý chức vụ', 'desc' => 'Thêm, sửa, khóa chức vụ'],
                                ['key' => PositionCapability::TRANSFER_EMPLOYEE, 'label' => 'Điều chuyển nhân sự', 'desc' => 'Chuyển nhân viên sang phòng ban khác'],
                            ],
                        ],
                        [
                            'group' => 'Duyệt & Ký kết',
                            'icon' => '✅',
                            'items' => [
                                ['key' => PositionCapability::APPROVE_REQUESTS, 'label' => 'Duyệt yêu cầu chung', 'desc' => 'Phê duyệt các yêu cầu từ nhân viên'],
                                ['key' => PositionCapability::SIGN_DOCUMENTS, 'label' => 'Ký duyệt văn bản', 'desc' => 'Ký và phê duyệt hợp đồng, quyết định'],
                            ],
                        ],
                        [
                            'group' => 'Báo cáo & Hệ thống',
                            'icon' => '📊',
                            'items' => [
                                ['key' => PositionCapability::VIEW_REPORTS, 'label' => 'Xem báo cáo tổng hợp', 'desc' => 'Dashboard và báo cáo toàn hệ thống'],
                                ['key' => PositionCapability::EXPORT_REPORTS, 'label' => 'Xuất báo cáo', 'desc' => 'Xuất dữ liệu Excel / PDF'],
                                ['key' => PositionCapability::VIEW_ACTIVITY_LOGS, 'label' => 'Xem nhật ký hoạt động', 'desc' => 'Xem lịch sử thao tác hệ thống'],
                            ],
                        ],
                    ],
                    'menuItems' => MenuBuilder::build($user),
                ];
            },
        ]);
    }

    private function availableCapabilities(): array
    {
        $codes = PositionCapability::all();

        if (!Schema::hasTable('position_capabilities')) {
            return $codes;
        }

        $dbCodes = PositionCapabilityModel::query()
            ->where('is_active', true)
            ->pluck('code')
            ->all();

        return array_values(array_unique(array_merge($codes, $dbCodes)));
    }
}
