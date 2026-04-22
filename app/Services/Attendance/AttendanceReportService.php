<?php

namespace App\Services\Attendance;

use App\Models\User;
use App\Services\AttendanceService;

class AttendanceReportService
{
    public function getReportData(AttendanceService $attendanceService, User $user, array $filters = []): array
    {
        return $attendanceService->buildReportData($user, $filters);
    }

    public function exportExcelHtml(AttendanceService $attendanceService, User $user, array $filters = []): array
    {
        return $attendanceService->buildExportExcelHtml($user, $filters);
    }

    public function exportPdfHtml(AttendanceService $attendanceService, User $user, array $filters = []): array
    {
        return $attendanceService->buildExportPdfHtml($user, $filters);
    }
}
