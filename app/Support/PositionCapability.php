<?php

namespace App\Support;

final class PositionCapability
{
    public const MANAGE_EMPLOYEES = 'manage_employees';
    public const VIEW_SALARY = 'view_salary';
    public const MANAGE_SALARY = 'manage_salary';

    public const APPROVE_ATTENDANCE = 'approve_attendance';
    public const VIEW_ALL_ATTENDANCE = 'view_all_attendance';
    public const EXPORT_ATTENDANCE = 'export_attendance';

    public const APPROVE_LEAVE = 'approve_leave';
    public const MANAGE_LEAVE_POLICY = 'manage_leave_policy';

    public const MANAGE_PROJECTS = 'manage_projects';
    public const MANAGE_PROJECT_MEMBERS = 'manage_project_members';
    public const MANAGE_PROJECT_ROLES = 'manage_project_roles';
    public const VIEW_ALL_PROJECTS = 'view_all_projects';

    public const MANAGE_DEPARTMENTS = 'manage_departments';
    public const MANAGE_POSITIONS = 'manage_positions';
    public const TRANSFER_EMPLOYEE = 'transfer_employee';

    public const APPROVE_REQUESTS = 'approve_requests';
    public const SIGN_DOCUMENTS = 'sign_documents';

    public const VIEW_REPORTS = 'view_reports';
    public const EXPORT_REPORTS = 'export_reports';
    public const VIEW_ACTIVITY_LOGS = 'view_activity_logs';

    public static function all(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * Capability metadata for DB seed and UI usage.
     *
     * @return array<string, array{module:string,name:string,description:string}>
     */
    public static function definitions(): array
    {
        return [
            self::MANAGE_EMPLOYEES => [
                'module' => 'human_resources',
                'name' => 'Quan ly nhan su',
                'description' => 'Them, sua, khoa nhan su',
            ],
            self::VIEW_SALARY => [
                'module' => 'human_resources',
                'name' => 'Xem luong nhan vien',
                'description' => 'Xem muc luong cua toan bo nhan su',
            ],
            self::MANAGE_SALARY => [
                'module' => 'human_resources',
                'name' => 'Dieu chinh luong',
                'description' => 'De xuat va phe duyet thay doi luong',
            ],
            self::APPROVE_ATTENDANCE => [
                'module' => 'attendance',
                'name' => 'Duyet cham cong',
                'description' => 'Xac nhan va phe duyet cong cua nhan vien',
            ],
            self::VIEW_ALL_ATTENDANCE => [
                'module' => 'attendance',
                'name' => 'Xem toan bo cham cong',
                'description' => 'Xem bao cao cong cua tat ca nhan su',
            ],
            self::EXPORT_ATTENDANCE => [
                'module' => 'attendance',
                'name' => 'Xuat bao cao cham cong',
                'description' => 'Xuat Excel / PDF',
            ],
            self::APPROVE_LEAVE => [
                'module' => 'leave',
                'name' => 'Phe duyet ngay nghi',
                'description' => 'Duyet hoac tu choi don xin nghi',
            ],
            self::MANAGE_LEAVE_POLICY => [
                'module' => 'leave',
                'name' => 'Quan ly chinh sach nghi',
                'description' => 'Thiet lap quy dinh nghi phep',
            ],
            self::MANAGE_PROJECTS => [
                'module' => 'project',
                'name' => 'Quan ly du an',
                'description' => 'Tao, sua, phan cong du an',
            ],
            self::MANAGE_PROJECT_MEMBERS => [
                'module' => 'project',
                'name' => 'Quan ly thanh vien du an',
                'description' => 'Them/xoa thanh vien',
            ],
            self::MANAGE_PROJECT_ROLES => [
                'module' => 'project',
                'name' => 'Quan ly vai tro du an',
                'description' => 'Tao va xoa vai tro trong tung du an',
            ],
            self::VIEW_ALL_PROJECTS => [
                'module' => 'project',
                'name' => 'Xem toan bo du an',
                'description' => 'Xem tat ca du an trong he thong',
            ],
            self::MANAGE_DEPARTMENTS => [
                'module' => 'organization',
                'name' => 'Quan ly phong ban',
                'description' => 'Them, sua, khoa phong ban',
            ],
            self::MANAGE_POSITIONS => [
                'module' => 'organization',
                'name' => 'Quan ly chuc vu',
                'description' => 'Them, sua, khoa chuc vu',
            ],
            self::TRANSFER_EMPLOYEE => [
                'module' => 'organization',
                'name' => 'Dieu chuyen nhan su',
                'description' => 'Chuyen nhan vien sang phong ban khac',
            ],
            self::APPROVE_REQUESTS => [
                'module' => 'approval',
                'name' => 'Duyet yeu cau',
                'description' => 'Phe duyet cac yeu cau tu nhan vien',
            ],
            self::SIGN_DOCUMENTS => [
                'module' => 'approval',
                'name' => 'Ky duyet van ban',
                'description' => 'Ky va phe duyet hop dong, quyet dinh',
            ],
            self::VIEW_REPORTS => [
                'module' => 'report',
                'name' => 'Xem bao cao tong hop',
                'description' => 'Dashboard va bao cao toan he thong',
            ],
            self::EXPORT_REPORTS => [
                'module' => 'report',
                'name' => 'Xuat bao cao',
                'description' => 'Xuat du lieu Excel / PDF',
            ],
            self::VIEW_ACTIVITY_LOGS => [
                'module' => 'report',
                'name' => 'Xem nhat ky hoat dong',
                'description' => 'Xem lich su thao tac he thong',
            ],
        ];
    }
}

