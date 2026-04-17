<?php

namespace App\Support;

final class PositionCapability
{
    public const MANAGE_EMPLOYEES = 'manage_employees';
    public const VIEW_ALL_PROFILES = 'view_all_profiles';
    public const VIEW_OWN_PROFILE = 'view_own_profile';
    public const UPDATE_OWN_PROFILE = 'update_own_profile';
    public const VIEW_SALARY = 'view_salary';
    public const VIEW_OWN_SALARY = 'view_own_salary';
    public const VIEW_ALL_SALARY = 'view_all_salary';
    public const MANAGE_SALARY = 'manage_salary';

    public const APPROVE_ATTENDANCE = 'approve_attendance';
    public const VIEW_ALL_ATTENDANCE = 'view_all_attendance';
    public const VIEW_OWN_ATTENDANCE = 'view_own_attendance';
    public const EXPORT_ATTENDANCE = 'export_attendance';
    public const CHECK_IN = 'check_in';
    public const CHECK_OUT = 'check_out';
    public const REQUEST_ATTENDANCE_ADJUSTMENT = 'request_attendance_adjustment';

    public const APPROVE_LEAVE = 'approve_leave';
    public const MANAGE_LEAVE_POLICY = 'manage_leave_policy';

    public const MANAGE_PROJECTS = 'manage_projects';
    public const MANAGE_PROJECT_MEMBERS = 'manage_project_members';
    public const MANAGE_PROJECT_ROLES = 'manage_project_roles';
    public const VIEW_ALL_PROJECTS = 'view_all_projects';
    public const VIEW_OWN_PROJECTS = 'view_own_projects';
    public const UPDATE_PROJECT_TASK_STATUS = 'update_project_task_status';

    public const MANAGE_DEPARTMENTS = 'manage_departments';
    public const MANAGE_POSITIONS = 'manage_positions';
    public const TRANSFER_EMPLOYEE = 'transfer_employee';

    public const APPROVE_REQUESTS = 'approve_requests';
    public const SIGN_DOCUMENTS = 'sign_documents';

    public const VIEW_REPORTS = 'view_reports';
    public const EXPORT_REPORTS = 'export_reports';
    public const VIEW_ACTIVITY_LOGS = 'view_activity_logs';
    public const VIEW_DASHBOARD = 'view_dashboard';

    public const VIEW_FEEDBACKS = 'view_feedbacks';
    public const CREATE_FEEDBACK = 'create_feedback';
    public const REPLY_FEEDBACK = 'reply_feedback';
    public const MANAGE_FEEDBACKS = 'manage_feedbacks';

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
            self::VIEW_ALL_PROFILES => [
                'module' => 'human_resources',
                'name' => 'Xem ho so nhan vien',
                'description' => 'Xem ho so cua toan bo nhan su',
            ],
            self::VIEW_OWN_PROFILE => [
                'module' => 'human_resources',
                'name' => 'Xem ho so ca nhan',
                'description' => 'Xem thong tin ho so cua chinh minh',
            ],
            self::UPDATE_OWN_PROFILE => [
                'module' => 'human_resources',
                'name' => 'Cap nhat ho so ca nhan',
                'description' => 'Cap nhat thong tin ho so ca nhan',
            ],
            self::VIEW_SALARY => [
                'module' => 'human_resources',
                'name' => 'Xem luong nhan vien',
                'description' => 'Xem muc luong cua toan bo nhan su',
            ],
            self::VIEW_OWN_SALARY => [
                'module' => 'salary',
                'name' => 'Xem luong ca nhan',
                'description' => 'Xem muc luong cua chinh minh',
            ],
            self::VIEW_ALL_SALARY => [
                'module' => 'salary',
                'name' => 'Xem luong toan cong ty',
                'description' => 'Xem muc luong cua toan bo nhan su',
            ],
            self::MANAGE_SALARY => [
                'module' => 'salary',
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
            self::VIEW_OWN_ATTENDANCE => [
                'module' => 'attendance',
                'name' => 'Xem cham cong ca nhan',
                'description' => 'Xem du lieu cham cong cua chinh minh',
            ],
            self::EXPORT_ATTENDANCE => [
                'module' => 'attendance',
                'name' => 'Xuat bao cao cham cong',
                'description' => 'Xuat Excel / PDF',
            ],
            self::CHECK_IN => [
                'module' => 'attendance',
                'name' => 'Cham cong vao',
                'description' => 'Thuc hien check-in',
            ],
            self::CHECK_OUT => [
                'module' => 'attendance',
                'name' => 'Cham cong ra',
                'description' => 'Thuc hien check-out',
            ],
            self::REQUEST_ATTENDANCE_ADJUSTMENT => [
                'module' => 'attendance',
                'name' => 'Yeu cau chinh cong',
                'description' => 'Tao yeu cau chinh cong',
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
            self::VIEW_OWN_PROJECTS => [
                'module' => 'project',
                'name' => 'Xem du an ca nhan',
                'description' => 'Xem cac du an minh tham gia',
            ],
            self::UPDATE_PROJECT_TASK_STATUS => [
                'module' => 'project',
                'name' => 'Cap nhat trang thai dau viec',
                'description' => 'Cap nhat trang thai task',
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
            self::VIEW_DASHBOARD => [
                'module' => 'system',
                'name' => 'Xem dashboard tong quan',
                'description' => 'Xem dashboard va thong tin tong quan',
            ],
            self::VIEW_FEEDBACKS => [
                'module' => 'feedback',
                'name' => 'Xem phan hoi',
                'description' => 'Xem danh sach feedback noi bo',
            ],
            self::CREATE_FEEDBACK => [
                'module' => 'feedback',
                'name' => 'Tao phan hoi',
                'description' => 'Gui feedback noi bo',
            ],
            self::REPLY_FEEDBACK => [
                'module' => 'feedback',
                'name' => 'Phan hoi feedback',
                'description' => 'Tra loi feedback noi bo',
            ],
            self::MANAGE_FEEDBACKS => [
                'module' => 'feedback',
                'name' => 'Quan ly phan hoi',
                'description' => 'Quan ly, xu ly feedback noi bo',
            ],
        ];
    }
}
