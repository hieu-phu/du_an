<?php

namespace App\Support;

/**
 * Danh sách đầy đủ các quyền hạn nghiệp vụ của chức vụ.
 * Mỗi constant là một capability key được lưu trong positions.capabilities (JSON).
 *
 * Cách sử dụng:
 *   Gate::allows(PositionCapability::APPROVE_ATTENDANCE)
 *   $user->hasPositionCapability(PositionCapability::VIEW_SALARY)
 */
final class PositionCapability
{
    // ── Nhân sự ──────────────────────────────────────────────────────────────
    public const MANAGE_EMPLOYEES   = 'manage_employees';
    public const VIEW_SALARY        = 'view_salary';
    public const MANAGE_SALARY      = 'manage_salary';

    // ── Chấm công ─────────────────────────────────────────────────────────────
    public const APPROVE_ATTENDANCE    = 'approve_attendance';
    public const VIEW_ALL_ATTENDANCE   = 'view_all_attendance';
    public const EXPORT_ATTENDANCE     = 'export_attendance';

    // ── Nghỉ phép ─────────────────────────────────────────────────────────────
    public const APPROVE_LEAVE         = 'approve_leave';
    public const MANAGE_LEAVE_POLICY   = 'manage_leave_policy';

    // ── Dự án ─────────────────────────────────────────────────────────────────
    public const MANAGE_PROJECTS        = 'manage_projects';
    public const MANAGE_PROJECT_MEMBERS = 'manage_project_members';
    public const VIEW_ALL_PROJECTS      = 'view_all_projects';

    // ── Phòng ban & Tổ chức ───────────────────────────────────────────────────
    public const MANAGE_DEPARTMENTS = 'manage_departments';
    public const MANAGE_POSITIONS   = 'manage_positions';
    public const TRANSFER_EMPLOYEE  = 'transfer_employee';

    // ── Duyệt & Ký kết ────────────────────────────────────────────────────────
    public const APPROVE_REQUESTS   = 'approve_requests';
    public const SIGN_DOCUMENTS     = 'sign_documents';

    // ── Báo cáo & Hệ thống ───────────────────────────────────────────────────
    public const VIEW_REPORTS        = 'view_reports';
    public const EXPORT_REPORTS      = 'export_reports';
    public const VIEW_ACTIVITY_LOGS  = 'view_activity_logs';

    /** Trả về toàn bộ danh sách capability keys. */
    public static function all(): array
    {
        return [
            self::MANAGE_EMPLOYEES,
            self::VIEW_SALARY,
            self::MANAGE_SALARY,
            self::APPROVE_ATTENDANCE,
            self::VIEW_ALL_ATTENDANCE,
            self::EXPORT_ATTENDANCE,
            self::APPROVE_LEAVE,
            self::MANAGE_LEAVE_POLICY,
            self::MANAGE_PROJECTS,
            self::MANAGE_PROJECT_MEMBERS,
            self::VIEW_ALL_PROJECTS,
            self::MANAGE_DEPARTMENTS,
            self::MANAGE_POSITIONS,
            self::TRANSFER_EMPLOYEE,
            self::APPROVE_REQUESTS,
            self::SIGN_DOCUMENTS,
            self::VIEW_REPORTS,
            self::EXPORT_REPORTS,
            self::VIEW_ACTIVITY_LOGS,
        ];
    }
}
