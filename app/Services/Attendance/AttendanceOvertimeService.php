<?php

namespace App\Services\Attendance;

use App\Models\ApprovalRequest;
use App\Models\AttendanceRequest;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Services\AttendanceService;

class AttendanceOvertimeService
{
    public function submitAttendanceRequest(AttendanceService $attendanceService, User $user, array $payload): AttendanceRequest|OvertimeRequest
    {
        return $attendanceService->handleSubmitAttendanceRequest($user, $payload);
    }

    public function reviewApprovalRequest(AttendanceService $attendanceService, ApprovalRequest $approvalRequest, User $reviewer, string $decision, ?string $note = null): ApprovalRequest
    {
        return $attendanceService->handleReviewApprovalRequest($approvalRequest, $reviewer, $decision, $note);
    }
}
