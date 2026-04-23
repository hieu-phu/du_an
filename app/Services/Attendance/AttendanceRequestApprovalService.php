<?php

namespace App\Services\Attendance;

use App\Models\ApprovalRequest;
use App\Models\AttendanceAdjustment;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRequest;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Services\AttendanceService;

class AttendanceRequestApprovalService
{
    public function confirmAttendance(AttendanceService $attendanceService, AttendanceRecord $record, User $approver, ?string $note = null, ?string $resolvedCheckOutTime = null): AttendanceRecord
    {
        return $attendanceService->handleConfirmAttendance($record, $approver, $note, $resolvedCheckOutTime);
    }

    public function confirmAttendanceBulk(AttendanceService $attendanceService, array $recordIds, User $approver, ?string $note = null): array
    {
        return $attendanceService->handleConfirmAttendanceBulk($recordIds, $approver, $note);
    }

    public function rejectAttendance(AttendanceService $attendanceService, AttendanceRecord $record, User $approver, ?string $note = null): AttendanceRecord
    {
        return $attendanceService->handleRejectAttendance($record, $approver, $note);
    }

    public function getApprovalsData(AttendanceService $attendanceService, array $filters = [], ?User $viewer = null): array
    {
        return $attendanceService->buildApprovalsData($filters, $viewer);
    }

    public function getMyAdjustmentData(AttendanceService $attendanceService, User $user): array
    {
        return $attendanceService->buildMyAdjustmentData($user);
    }

    public function submitAttendanceRequest(AttendanceService $attendanceService, User $user, array $payload): AttendanceRequest|OvertimeRequest
    {
        return $attendanceService->handleSubmitAttendanceRequest($user, $payload);
    }

    public function submitAttendanceAdjustmentRequest(AttendanceService $attendanceService, User $user, array $payload): AttendanceAdjustment
    {
        return $attendanceService->handleSubmitAttendanceAdjustmentRequest($user, $payload);
    }

    public function reviewApprovalRequest(AttendanceService $attendanceService, ApprovalRequest $approvalRequest, User $reviewer, string $decision, ?string $note = null): ApprovalRequest
    {
        return $attendanceService->handleReviewApprovalRequest($approvalRequest, $reviewer, $decision, $note);
    }

    public function reviewApprovalRequestsBulk(AttendanceService $attendanceService, array $approvalRequestIds, User $reviewer, string $decision, ?string $note = null): array
    {
        return $attendanceService->handleReviewApprovalRequestsBulk($approvalRequestIds, $reviewer, $decision, $note);
    }
}
