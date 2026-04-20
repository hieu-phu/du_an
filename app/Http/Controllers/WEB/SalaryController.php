<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AttendanceAdjustment;
use App\Models\AttendanceRecord;
use App\Models\EmployeeProfile;
use App\Models\Holiday;
use App\Models\OvertimeRequest;
use App\Models\SalaryHistory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SalaryController extends Controller
{
    private const TIMEZONE = 'Asia/Ho_Chi_Minh';
    private const DEFAULT_FULL_DAY_MINUTES = 480;
    private const DEFAULT_HALF_DAY_MINUTES = 240;
    private const OVERTIME_WEEKDAY_MULTIPLIER = 1.5;
    private const OVERTIME_WEEKEND_MULTIPLIER = 2.0;
    private const OVERTIME_HOLIDAY_MULTIPLIER = 3.0;

    public function mine(Request $request): Response
    {
        $now = Carbon::now(self::TIMEZONE);
        $month = (int) $request->integer('month', $now->month);
        $year = (int) $request->integer('year', $now->year);

        if ($month < 1 || $month > 12) {
            $month = (int) $now->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = (int) $now->year;
        }

        $profile = $request->user()
            ->employeeProfile()
            ->with(['user:id,name,email', 'department:id,name', 'position:id,name'])
            ->firstOrFail();

        $records = AttendanceRecord::query()
            ->with([
                'workShift:id,shift_name,start_time,end_time,break_start_time,break_end_time,standard_minutes,half_day_minutes,handover_break_minutes,grace_minutes,late_grace_minutes,early_leave_grace_minutes,is_overnight',
                'workShift.overtimeRule:id,work_shift_id,hourly_rate',
            ])
            ->where('employee_profile_id', $profile->id)
            ->whereMonth('work_date', $month)
            ->whereYear('work_date', $year)
            ->orderBy('work_date')
            ->get();

        $baseSalary = (float) ($profile->base_salary ?? 0);
        $holidayMap = $this->holidayMap($month, $year);
        $expectedWorkDays = $this->countExpectedWorkDays($month, $year, $holidayMap);
        $dailyRate = $expectedWorkDays > 0 ? $baseSalary / $expectedWorkDays : 0.0;
        $hourlyRate = $dailyRate / 8;
        $paidHolidayRows = $this->buildPaidHolidayRows($records, $holidayMap, $dailyRate);
        $paidHolidayWorkUnits = (float) $paidHolidayRows->sum('work_unit');

        $approvedRecords = $records->filter(fn (AttendanceRecord $record) => $this->isEffectivelyApproved($record));
        $pendingRecords = $records->filter(fn (AttendanceRecord $record) => !$this->isEffectivelyApproved($record) && ($record->approval_status ?? 'pending') === 'pending');

        $approvedWorkUnits = (float) $approvedRecords->sum(fn (AttendanceRecord $record) => $this->resolvePayableWorkUnit($record)) + $paidHolidayWorkUnits;
        $pendingWorkUnits = (float) $pendingRecords->sum(fn (AttendanceRecord $record) => $this->resolvePayableWorkUnit($record));
        $approvedOvertimeMinutes = (int) $records->sum(fn (AttendanceRecord $record) => $this->resolveApprovedOvertimeMinutes($record));
        $payableWorkUnits = min($approvedWorkUnits, (float) $expectedWorkDays);
        $unpaidWorkUnits = max(0.0, (float) $expectedWorkDays - $approvedWorkUnits - $pendingWorkUnits);
        $baseSalaryAmount = $dailyRate * $payableWorkUnits;
        $pendingAmount = $dailyRate * $pendingWorkUnits;
        $deductionAmount = $dailyRate * $unpaidWorkUnits;
        $overtimeAmount = $records->sum(fn (AttendanceRecord $record) => $this->resolveOvertimeAmount($record, $hourlyRate, $holidayMap));

        $salaryHistory = SalaryHistory::query()
            ->where('employee_profile_id', $profile->id)
            ->orderByDesc('effective_date')
            ->limit(10)
            ->get()
            ->map(fn (SalaryHistory $history) => [
                'id' => $history->id,
                'old_salary' => (float) $history->old_salary,
                'new_salary' => (float) $history->new_salary,
                'currency' => $history->currency ?: 'VND',
                'effective_date' => optional($history->effective_date)->format('Y-m-d'),
                'note' => $history->note,
            ])
            ->values();

        return Inertia::render('Salary/My', [
            'filters' => [
                'month' => $month,
                'year' => $year,
                'months' => collect(range(1, 12))
                    ->map(fn (int $item) => [
                        'value' => $item,
                        'label' => 'Thang ' . str_pad((string) $item, 2, '0', STR_PAD_LEFT),
                    ])
                    ->values(),
                'years' => collect(range($now->year - 3, $now->year + 1))
                    ->map(fn (int $item) => [
                        'value' => $item,
                        'label' => (string) $item,
                    ])
                    ->values(),
            ],
            'profile' => [
                'employee_code' => $profile->employee_code,
                'name' => $profile->user?->name,
                'email' => $profile->user?->email,
                'department' => $profile->department?->name,
                'position' => $profile->position?->name,
                'employment_type' => $profile->employment_type,
                'currency' => $profile->salary_currency ?: 'VND',
            ],
            'summary' => [
                'base_salary' => round($baseSalary, 2),
                'expected_work_days' => $expectedWorkDays,
                'daily_rate' => round($dailyRate, 2),
                'hourly_rate' => round($hourlyRate, 2),
                'approved_work_units' => round($approvedWorkUnits, 2),
                'pending_work_units' => round($pendingWorkUnits, 2),
                'unpaid_work_units' => round($unpaidWorkUnits, 2),
                'approved_overtime_minutes' => $approvedOvertimeMinutes,
                'base_salary_amount' => round($baseSalaryAmount, 2),
                'overtime_amount' => round($overtimeAmount, 2),
                'pending_amount' => round($pendingAmount, 2),
                'deduction_amount' => round($deductionAmount, 2),
                'gross_amount' => round($baseSalaryAmount + $overtimeAmount, 2),
                'net_amount' => round($baseSalaryAmount + $overtimeAmount, 2),
                'total_records' => $records->count(),
                'approved_records' => $approvedRecords->count(),
                'pending_records' => $pendingRecords->count(),
                'rejected_records' => $records->where('approval_status', 'rejected')->count(),
            ],
            'records' => $records
                ->map(fn (AttendanceRecord $record) => [
                    'id' => $record->id,
                    'work_date' => optional($record->work_date)->format('Y-m-d'),
                    'shift_name' => $this->resolveShiftName($record),
                    'shift_time_range' => $this->resolveShiftTimeRange($record),
                    'shift_standard_minutes' => $this->resolveShiftStandardMinutes($record),
                    'shift_half_day_minutes' => $this->resolveShiftHalfDayMinutes($record),
                    'worked_minutes' => $this->resolveWorkedMinutes($record),
                    'work_unit' => $this->resolvePayableWorkUnit($record),
                    'payable_amount' => round($dailyRate * $this->resolvePayableWorkUnit($record), 2),
                    'overtime_minutes' => $this->resolveApprovedOvertimeMinutes($record),
                    'overtime_multiplier' => $this->resolveOvertimeMultiplier($record, $holidayMap),
                    'overtime_type_label' => $this->resolveOvertimeTypeLabel($record, $holidayMap),
                    'overtime_amount' => round($this->resolveOvertimeAmount($record, $hourlyRate, $holidayMap), 2),
                    'attendance_status' => $this->resolvePayableAttendanceStatus($record),
                    'day_status' => $this->resolvePayableDayStatus($record),
                    'approval_status' => $this->resolveApprovalStatus($record),
                    'approval_note' => $record->approval_note,
                ])
                ->concat($paidHolidayRows)
                ->sortBy('work_date')
                ->values(),
            'salaryHistory' => $salaryHistory,
        ]);
    }

    private function countExpectedWorkDays(int $month, int $year, ?array $holidayMap = null): int
    {
        $start = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $holidayMap ??= $this->holidayMap($month, $year);
        $count = 0;

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            $holiday = $holidayMap[$date->toDateString()] ?? null;
            if ($holiday && !($holiday['is_paid_leave'] ?? false)) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    private function holidayMap(int $month, int $year): array
    {
        $start = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return Holiday::query()
            ->whereBetween('holiday_date', [$start->toDateString(), $end->toDateString()])
            ->get(['holiday_date', 'holiday_name', 'holiday_type', 'is_paid_leave'])
            ->mapWithKeys(function (Holiday $holiday) {
                $date = Carbon::parse($holiday->holiday_date, self::TIMEZONE)->toDateString();

                return [$date => [
                    'holiday_name' => $holiday->holiday_name,
                    'holiday_type' => $holiday->holiday_type,
                    'is_paid_leave' => (bool) $holiday->is_paid_leave,
                ]];
            })
            ->all();
    }

    private function buildPaidHolidayRows($records, array $holidayMap, float $dailyRate)
    {
        $recordDates = $records
            ->map(fn (AttendanceRecord $record) => optional($record->work_date)->format('Y-m-d'))
            ->filter()
            ->flip();

        return collect($holidayMap)
            ->filter(fn (array $holiday, string $date) => (bool) ($holiday['is_paid_leave'] ?? false) && !$recordDates->has($date))
            ->reject(fn (array $holiday, string $date) => Carbon::parse($date, self::TIMEZONE)->isWeekend())
            ->map(fn (array $holiday, string $date) => [
                'id' => 'holiday-' . $date,
                'work_date' => $date,
                'shift_name' => $holiday['holiday_name'] ?? 'Ngay le',
                'shift_time_range' => 'Nghi le co luong',
                'shift_standard_minutes' => self::DEFAULT_FULL_DAY_MINUTES,
                'shift_half_day_minutes' => self::DEFAULT_HALF_DAY_MINUTES,
                'worked_minutes' => 0,
                'work_unit' => 1.0,
                'payable_amount' => round($dailyRate, 2),
                'overtime_minutes' => 0,
                'overtime_multiplier' => self::OVERTIME_HOLIDAY_MULTIPLIER,
                'overtime_type_label' => 'Ngay le',
                'overtime_amount' => 0.0,
                'attendance_status' => 'on_time',
                'day_status' => 'holiday_paid',
                'approval_status' => 'approved',
                'approval_note' => 'Ngay le co luong',
            ])
            ->values();
    }

    private function resolvePayableWorkUnit(AttendanceRecord $record): float
    {
        $dayStatus = $this->resolvePayableDayStatus($record);

        if (in_array($dayStatus, ['leave', 'business_trip'], true)) {
            return 1.0;
        }

        if (in_array($dayStatus, ['unpaid_leave', 'absent'], true)) {
            return 0.0;
        }

        if ($record->missing_check_in || $record->missing_check_out) {
            return 0.0;
        }

        $workedMinutes = $this->resolveWorkedMinutes($record);
        $fullDayMinutes = $this->resolveShiftStandardMinutes($record);
        $halfDayMinutes = $this->resolveShiftHalfDayMinutes($record);

        if (
            $record->check_in_at
            && $record->check_out_at
            && $this->resolveLateMinutes($record) === 0
            && $this->resolveEarlyLeaveMinutes($record) === 0
            && $this->resolvePayableAttendanceStatus($record) !== 'absent'
        ) {
            return 1.0;
        }

        if ($workedMinutes >= max(1, $fullDayMinutes)) {
            return 1.0;
        }

        if ($workedMinutes >= max(1, $halfDayMinutes)) {
            return 0.5;
        }

        return 0.0;
    }

    private function resolvePayableAttendanceStatus(AttendanceRecord $record): string
    {
        if ((string) ($record->attendance_status ?? '') === 'absent' && !$record->check_in_at) {
            return 'absent';
        }

        return $this->resolveLateMinutes($record) > 0 ? 'late' : 'on_time';
    }

    private function resolvePayableDayStatus(AttendanceRecord $record): string
    {
        $dayStatus = (string) ($record->day_status ?? '');

        if (in_array($dayStatus, ['leave', 'unpaid_leave', 'business_trip', 'absent'], true)) {
            return $dayStatus;
        }

        if ($record->missing_check_in) {
            return 'missing_check_in';
        }

        if ($record->missing_check_out || ($record->check_in_at && !$record->check_out_at)) {
            return 'missing_check_out';
        }

        if ($this->resolveEarlyLeaveMinutes($record) > 0) {
            return 'early_leave';
        }

        if ($this->resolveLateMinutes($record) > 0) {
            return 'late';
        }

        return 'present';
    }

    private function resolveApprovalStatus(AttendanceRecord $record): string
    {
        if (($record->approval_status ?? null) === 'rejected') {
            return 'rejected';
        }

        return $this->isEffectivelyApproved($record) ? 'approved' : 'pending';
    }

    private function isEffectivelyApproved(AttendanceRecord $record): bool
    {
        if (($record->approval_status ?? null) === 'approved' || (bool) $record->is_confirmed) {
            return true;
        }

        return AttendanceAdjustment::query()
            ->where('attendance_record_id', $record->id)
            ->where('status', 'approved')
            ->exists();
    }

    private function resolveOvertimeMultiplier(AttendanceRecord $record, array $holidayMap): float
    {
        $workDate = optional($record->work_date)?->format('Y-m-d');

        if (!$workDate) {
            return self::OVERTIME_WEEKDAY_MULTIPLIER;
        }

        if (isset($holidayMap[$workDate])) {
            return self::OVERTIME_HOLIDAY_MULTIPLIER;
        }

        $date = Carbon::parse($workDate, self::TIMEZONE);

        return $date->isWeekend()
            ? self::OVERTIME_WEEKEND_MULTIPLIER
            : self::OVERTIME_WEEKDAY_MULTIPLIER;
    }

    private function resolveOvertimeTypeLabel(AttendanceRecord $record, array $holidayMap): string
    {
        $workDate = optional($record->work_date)?->format('Y-m-d');

        if ($workDate && isset($holidayMap[$workDate])) {
            return 'Ngay le';
        }

        if ($workDate && Carbon::parse($workDate, self::TIMEZONE)->isWeekend()) {
            return 'Ngay nghi tuan';
        }

        return 'Ngay thuong';
    }

    private function resolveOvertimeAmount(AttendanceRecord $record, float $hourlyRate, array $holidayMap): float
    {
        $overtimeMinutes = $this->resolveApprovedOvertimeMinutes($record);
        if ($overtimeMinutes === 0) {
            return 0.0;
        }

        $snapshot = $this->resolveShiftSnapshot($record);
        $configuredHourlyRate = (float) ($snapshot['overtime_hourly_rate'] ?? 0);

        if ($configuredHourlyRate > 0) {
            return $configuredHourlyRate * ($overtimeMinutes / 60);
        }

        return $hourlyRate * ($overtimeMinutes / 60) * $this->resolveOvertimeMultiplier($record, $holidayMap);
    }

    private function resolveApprovedOvertimeMinutes(AttendanceRecord $record): int
    {
        $workDate = optional($record->work_date)?->format('Y-m-d');

        if (!$workDate) {
            return 0;
        }

        return (int) OvertimeRequest::query()
            ->where('employee_profile_id', (int) $record->employee_profile_id)
            ->whereDate('work_date', $workDate)
            ->where('status', 'approved')
            ->sum('approved_minutes');
    }

    private function resolveWorkedMinutes(AttendanceRecord $record): int
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        if (!$record->check_in_at || !$record->check_out_at) {
            return max(0, (int) ($record->worked_minutes ?? 0));
        }

        $minutes = max(0, (int) $record->check_in_at->diffInMinutes($record->check_out_at, false));
        $breakMinutes = $this->resolveBreakOverlapMinutes($record, $snapshot);
        $remainingMinutes = max(0, $minutes - $breakMinutes);
        $handoverMinutes = min($remainingMinutes, max(0, (int) ($snapshot['handover_break_minutes'] ?? 0)));

        return max(0, $remainingMinutes - $handoverMinutes);
    }

    private function resolveLateMinutes(AttendanceRecord $record): int
    {
        if (!$record->check_in_at) {
            return max(0, (int) ($record->late_minutes ?? 0));
        }

        $checkInAt = Carbon::parse($record->check_in_at, self::TIMEZONE);
        [$expectedStart] = $this->shiftBoundaries($checkInAt, $this->resolveShiftSnapshot($record));
        $allowedMinutes = $this->resolveGraceMinutes($record, 'late_grace_minutes', 10);
        $diffMinutes = (int) $expectedStart->diffInMinutes($checkInAt, false);

        return max(0, $diffMinutes - $allowedMinutes);
    }

    private function resolveEarlyLeaveMinutes(AttendanceRecord $record): int
    {
        if (!$record->check_out_at) {
            return max(0, (int) ($record->early_leave_minutes ?? 0));
        }

        $checkOutAt = Carbon::parse($record->check_out_at, self::TIMEZONE);
        [, $expectedEnd] = $this->shiftBoundaries($checkOutAt, $this->resolveShiftSnapshot($record));
        $allowedMinutes = $this->resolveGraceMinutes($record, 'early_leave_grace_minutes', 5);
        $diffMinutes = (int) $expectedEnd->diffInMinutes($checkOutAt, false);

        return $diffMinutes < 0 ? max(0, abs($diffMinutes) - $allowedMinutes) : 0;
    }

    private function resolveGraceMinutes(AttendanceRecord $record, string $specificKey, int $defaultMinutes): int
    {
        $snapshot = $this->resolveShiftSnapshot($record);
        $minutes = (int) (($snapshot[$specificKey] ?? null) ?? ($snapshot['grace_minutes'] ?? $defaultMinutes));

        return $minutes > 0 ? $minutes : $defaultMinutes;
    }

    private function shiftBoundaries(Carbon $dateTime, array $snapshot): array
    {
        $startTime = (string) ($snapshot['start_time'] ?? '08:00:00');
        $endTime = (string) ($snapshot['end_time'] ?? '17:30:00');
        $start = $this->resolveTimePoint($dateTime, $startTime);
        $end = $this->resolveTimePoint(
            $dateTime,
            $endTime,
            (bool) ($snapshot['is_overnight'] ?? false) && $this->minutesOfDay($endTime) <= $this->minutesOfDay($startTime)
        );

        return [$start, $end];
    }

    private function resolveBreakOverlapMinutes(AttendanceRecord $record, array $snapshot): int
    {
        $breakStart = $snapshot['break_start_time'] ?? null;
        $breakEnd = $snapshot['break_end_time'] ?? null;

        if (!$breakStart || !$breakEnd || !$record->check_in_at || !$record->check_out_at) {
            return 0;
        }

        $checkInAt = Carbon::parse($record->check_in_at, self::TIMEZONE);
        $checkOutAt = Carbon::parse($record->check_out_at, self::TIMEZONE);
        $breakStartAt = $this->resolveTimePoint($checkInAt, (string) $breakStart);
        $breakEndAt = $this->resolveTimePoint(
            $checkInAt,
            (string) $breakEnd,
            (bool) ($snapshot['is_overnight'] ?? false) && $this->minutesOfDay((string) $breakEnd) <= $this->minutesOfDay((string) $breakStart)
        );

        $start = $checkInAt->greaterThan($breakStartAt) ? $checkInAt : $breakStartAt;
        $end = $checkOutAt->lessThan($breakEndAt) ? $checkOutAt : $breakEndAt;

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return max(0, (int) $start->diffInMinutes($end, false));
    }

    private function resolveShiftStandardMinutes(AttendanceRecord $record): int
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        return max(1, (int) ($snapshot['standard_minutes'] ?? self::DEFAULT_FULL_DAY_MINUTES));
    }

    private function resolveShiftHalfDayMinutes(AttendanceRecord $record): int
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        return max(1, (int) ($snapshot['half_day_minutes'] ?? self::DEFAULT_HALF_DAY_MINUTES));
    }

    private function resolveShiftName(AttendanceRecord $record): string
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        return (string) ($snapshot['shift_name'] ?? $record->workShift?->shift_name ?? 'Ca mac dinh');
    }

    private function resolveShiftTimeRange(AttendanceRecord $record): string
    {
        $snapshot = $this->resolveShiftSnapshot($record);
        $startTime = $snapshot['start_time'] ?? $record->workShift?->start_time;
        $endTime = $snapshot['end_time'] ?? $record->workShift?->end_time;
        $isOvernight = (bool) ($snapshot['is_overnight'] ?? $record->workShift?->is_overnight ?? false);

        if (!$startTime || !$endTime) {
            return '-';
        }

        return substr((string) $startTime, 0, 5) . ' - ' . substr((string) $endTime, 0, 5) . ($isOvernight ? ' (+1)' : '');
    }

    private function resolveShiftSnapshot(AttendanceRecord $record): array
    {
        $snapshot = is_array($record->shift_snapshot) ? $record->shift_snapshot : [];
        $liveConfig = $record->workShift ? [
            'shift_name' => $record->workShift->shift_name,
            'start_time' => $record->workShift->start_time,
            'end_time' => $record->workShift->end_time,
            'break_start_time' => $record->workShift->break_start_time,
            'break_end_time' => $record->workShift->break_end_time,
            'standard_minutes' => $record->workShift->standard_minutes,
            'half_day_minutes' => $record->workShift->half_day_minutes,
            'handover_break_minutes' => $record->workShift->handover_break_minutes,
            'grace_minutes' => $record->workShift->grace_minutes,
            'late_grace_minutes' => $record->workShift->late_grace_minutes,
            'early_leave_grace_minutes' => $record->workShift->early_leave_grace_minutes,
            'overtime_hourly_rate' => $record->workShift->overtimeRule?->hourly_rate,
            'is_overnight' => $record->workShift->is_overnight,
        ] : [];

        return array_merge($liveConfig, $snapshot);
    }

    private function resolveTimePoint(Carbon $baseDate, string $time, bool $nextDay = false): Carbon
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));
        $point = $baseDate->copy()->startOfDay()->setTime($hour, $minute);

        return $nextDay ? $point->addDay() : $point;
    }

    private function minutesOfDay(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return ($hour * 60) + $minute;
    }
}
