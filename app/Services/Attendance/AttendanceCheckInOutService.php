<?php

namespace App\Services\Attendance;

use App\Models\AttendanceRecord;
use App\Services\AttendanceService;

class AttendanceCheckInOutService
{
    public function checkIn(AttendanceService $attendanceService, int $employeeProfileId, ?string $ipAddress, ?string $userAgent): AttendanceRecord
    {
        return $attendanceService->handleCheckIn($employeeProfileId, $ipAddress, $userAgent);
    }

    public function checkOut(AttendanceService $attendanceService, int $employeeProfileId, ?string $ipAddress, ?string $userAgent): AttendanceRecord
    {
        return $attendanceService->handleCheckOut($employeeProfileId, $ipAddress, $userAgent);
    }
}
