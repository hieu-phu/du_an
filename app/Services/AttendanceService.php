<?php

namespace App\Services;

use App\Repositories\AttendanceRepository;

class AttendanceService extends BaseService
{
    public function __construct(
        protected AttendanceRepository $attendanceRepository
    ) {}

    public function checkIn($employeeProfileId, $ipAddress, $userAgent)
    {
        // Business logic for check-in
        $today = now('Asia/Ho_Chi_Minh')->toDateString();

        $existingRecord = $this->attendanceRepository->model()
            ->where('employee_profile_id', $employeeProfileId)
            ->where('work_date', $today)
            ->first();

        if ($existingRecord) {
            throw new \Exception('Bạn đã check-in hôm nay rồi!');
        }

        return $this->attendanceRepository->create([
            'employee_profile_id' => $employeeProfileId,
            'work_date' => $today,
            'check_in_at' => now('Asia/Ho_Chi_Minh'),
            'attendance_status' => 'present',
        ]);
    }

    public function checkOut($employeeProfileId, $ipAddress, $userAgent)
    {
        // Business logic for check-out
        $today = now('Asia/Ho_Chi_Minh')->toDateString();

        $record = $this->attendanceRepository->model()
            ->where('employee_profile_id', $employeeProfileId)
            ->where('work_date', $today)
            ->first();

        if (!$record) {
            throw new \Exception('Bạn chưa check-in hôm nay!');
        }

        if ($record->check_out_at) {
            throw new \Exception('Bạn đã check-out hôm nay rồi!');
        }

        $checkOutTime = now('Asia/Ho_Chi_Minh');
        $checkInTime = \Carbon\Carbon::parse($record->check_in_at);
        $workedMinutes = $checkOutTime->diffInMinutes($checkInTime);

        return $this->attendanceRepository->update($record->id, [
            'check_out_at' => $checkOutTime,
            'worked_minutes' => $workedMinutes,
        ]);
    }
}
