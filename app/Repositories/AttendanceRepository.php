<?php

namespace App\Repositories;

use App\Models\AttendanceRecord;

class AttendanceRepository extends BaseRepository
{
    public function __construct(AttendanceRecord $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy số lượng bản ghi quên chấm công trong 30 ngày qua
     */
    public function getMissedPunchCount(int $profileId): int
    {
        return $this->model->newQuery()
            ->where('employee_profile_id', $profileId)
            ->where(function ($query) {
                $query->where('missing_check_in', true)
                    ->orWhere('missing_check_out', true);
            })
            ->where('work_date', '>=', now()->subDays(30))
            ->count();
    }

    /**
     * Lấy số lượng đi muộn trong tháng hiện tại
     */
    public function getFrequentLateCount(int $profileId): int
    {
        return $this->model->newQuery()
            ->where('employee_profile_id', $profileId)
            ->where('attendance_status', 'late')
            ->whereMonth('work_date', now()->month)
            ->whereYear('work_date', now()->year)
            ->count();
    }

    /**
     * Lấy số bản ghi chưa xác nhận trong tháng hiện tại
     */
    public function getUnconfirmedRecordsCount(int $profileId): int
    {
        return $this->model->newQuery()
            ->where('employee_profile_id', $profileId)
            ->where('is_confirmed', false)
            ->whereMonth('work_date', now()->month)
            ->whereYear('work_date', now()->year)
            ->where('work_date', '<', now()->toDateString())
            ->count();
    }

    /**
     * Lấy danh sách bản ghi chờ duyệt (Dành cho quản lý)
     */
    public function getPendingApprovalsCount(): int
    {
        return $this->model->newQuery()
            ->where('approval_status', 'pending')
            ->count();
    }
}
