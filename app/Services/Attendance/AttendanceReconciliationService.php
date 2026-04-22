<?php

namespace App\Services\Attendance;

use App\Models\AttendanceMonthLock;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;

class AttendanceReconciliationService
{
    public function getMyAttendanceData(AttendanceService $attendanceService, User $user, ?int $month = null, ?int $year = null): array
    {
        return $attendanceService->buildMyAttendanceData($user, $month, $year);
    }

    public function lockMonth(AttendanceService $attendanceService, User $actor, int $month, int $year, ?string $note = null): AttendanceMonthLock
    {
        return $attendanceService->handleLockMonth($actor, $month, $year, $note);
    }

    public function unlockMonth(AttendanceService $attendanceService, User $actor, int $month, int $year, ?string $note = null): AttendanceMonthLock
    {
        return $attendanceService->handleUnlockMonth($actor, $month, $year, $note);
    }

    public function markAbsencesForDate(AttendanceService $attendanceService, Carbon|string|null $date = null): int
    {
        return $attendanceService->handleMarkAbsencesForDate($date);
    }

    public function closeUnexplainedAbsences(AttendanceService $attendanceService, int $days = 2, Carbon|string|null $asOf = null): int
    {
        return $attendanceService->handleCloseUnexplainedAbsences($days, $asOf);
    }
}
