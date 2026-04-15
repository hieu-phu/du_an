<?php

namespace App\Services;

use App\Models\AttendanceApproval;
use App\Models\AttendanceEvent;
use App\Models\AttendanceMonthLock;
use App\Models\AttendanceRequest;
use App\Models\Holiday;
use App\Models\AttendanceMonthlySummary;
use App\Models\AttendanceRecord;
use App\Models\ApprovalRequest;
use App\Models\EmployeeProfile;
use App\Models\EmployeeWorkShiftAssignment;
use App\Models\ExportHistory;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Models\WorkShift;
use App\Repositories\AttendanceRepository;
use App\Support\AccessMatrix;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AttendanceService extends BaseService
{
    private const TIMEZONE = 'Asia/Ho_Chi_Minh';
    private const WORK_START_HOUR = 8;
    private const WORK_START_MINUTE = 0;

    public function __construct(
        protected AttendanceRepository $attendanceRepository,
        protected NotificationService $notificationService
    ) {}

    public function checkIn(int $employeeProfileId, ?string $ipAddress, ?string $userAgent): AttendanceRecord
    {
        return $this->handleTransaction(function () use ($employeeProfileId, $ipAddress, $userAgent) {
            $now = now(self::TIMEZONE);
            $today = $now->toDateString();
            $profile = EmployeeProfile::query()->with(['department', 'defaultWorkShift'])->findOrFail($employeeProfileId);

            $this->ensureMonthNotLocked($profile, $now);

            $existingRecord = $this->baseRecordQuery()
                ->where('employee_profile_id', $employeeProfileId)
                ->whereDate('work_date', $today)
                ->first();

            if ($existingRecord) {
                throw new \RuntimeException('Ban da check-in hom nay roi.');
            }

            $workShift = $this->resolveWorkShift($profile, $now);
            $lateMinutes = $this->determineLateMinutes($now, $workShift);
            $attendanceStatus = $lateMinutes > 0 ? 'late' : 'on_time';

            $record = $this->attendanceRepository->create([
                'employee_profile_id' => $employeeProfileId,
                'work_shift_id' => $workShift?->id,
                'work_date' => $today,
                'check_in_at' => $now,
                'check_in_ip_address' => $ipAddress,
                'check_in_device' => $userAgent,
                'worked_minutes' => 0,
                'late_minutes' => $lateMinutes,
                'early_leave_minutes' => 0,
                'overtime_minutes' => 0,
                'missing_check_in' => false,
                'missing_check_out' => true,
                'attendance_status' => $attendanceStatus,
                'approval_status' => 'pending',
                'day_status' => $attendanceStatus === 'late' ? 'late' : 'present',
                'is_confirmed' => false,
                'shift_snapshot' => $this->buildShiftSnapshot($workShift),
            ]);

            AttendanceEvent::query()->create([
                'attendance_record_id' => $record->id,
                'employee_profile_id' => $employeeProfileId,
                'event_type' => 'check_in',
                'event_at' => $now,
                'source' => 'web',
                'ip_address' => $ipAddress,
                'device_info' => $userAgent,
                'note' => $this->normalizeEventNote('Check in', $userAgent),
                'created_by' => $this->user()?->getAuthIdentifier(),
            ]);

            $this->refreshMonthlySummary($record);
            $this->notifyHr($record, 'check_in');
            $this->audit('attendance', 'check_in', "Check in attendance record #{$record->id}", 'attendance_records', $record->id);

            return $record->fresh(['employeeProfile.user', 'employeeProfile.department', 'employeeProfile.position']);
        });
    }

    public function checkOut(int $employeeProfileId, ?string $ipAddress, ?string $userAgent): AttendanceRecord
    {
        return $this->handleTransaction(function () use ($employeeProfileId, $ipAddress, $userAgent) {
            $now = now(self::TIMEZONE);
            $today = $now->toDateString();

            $record = $this->baseRecordQuery()
                ->where('employee_profile_id', $employeeProfileId)
                ->whereDate('work_date', $today)
                ->first();

            if (!$record) {
                throw new \RuntimeException('Ban chua check-in hom nay.');
            }

            if ($record->check_out_at) {
                throw new \RuntimeException('Ban da check-out hom nay roi.');
            }

            $record->loadMissing(['employeeProfile.department', 'workShift']);
            $this->ensureMonthNotLocked($record->employeeProfile, $now);

            $checkInTime = Carbon::parse($record->check_in_at, self::TIMEZONE);
            $workedMinutes = $this->calculateWorkedMinutes($checkInTime, $now);
            $workShift = $record->workShift ?: $this->resolveWorkShift($record->employeeProfile, $now);
            $lateMinutes = $this->determineLateMinutes($checkInTime, $workShift);
            $earlyLeaveMinutes = $this->determineEarlyLeaveMinutes($now, $workShift);
            $overtimeMinutes = $this->determineOvertimeMinutes($workedMinutes, $workShift);
            $dayStatus = $this->determineDayStatus($checkInTime, $now, $workShift);

            $record->update([
                'check_out_at' => $now,
                'check_out_ip_address' => $ipAddress,
                'check_out_device' => $userAgent,
                'worked_minutes' => $workedMinutes,
                'late_minutes' => $lateMinutes,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'missing_check_out' => false,
                'attendance_status' => $lateMinutes > 0 ? 'late' : 'on_time',
                'day_status' => $dayStatus,
                'work_shift_id' => $workShift?->id,
                'shift_snapshot' => $this->buildShiftSnapshot($workShift),
            ]);

            AttendanceEvent::query()->create([
                'attendance_record_id' => $record->id,
                'employee_profile_id' => $employeeProfileId,
                'event_type' => 'check_out',
                'event_at' => $now,
                'source' => 'web',
                'ip_address' => $ipAddress,
                'device_info' => $userAgent,
                'note' => $this->normalizeEventNote('Check out', $userAgent),
                'created_by' => $this->user()?->getAuthIdentifier(),
            ]);

            $this->refreshMonthlySummary($record->fresh());
            $this->notifyHr($record->fresh(['employeeProfile.user', 'employeeProfile.department', 'employeeProfile.position']), 'check_out');
            $this->audit('attendance', 'check_out', "Check out attendance record #{$record->id}", 'attendance_records', $record->id);

            return $record->fresh(['employeeProfile.user', 'employeeProfile.department', 'employeeProfile.position']);
        });
    }

    public function confirmAttendance(AttendanceRecord $record, User $approver, ?string $note = null): AttendanceRecord
    {
        return $this->handleTransaction(function () use ($record, $approver, $note) {
            if ($record->is_confirmed) {
                return $record->fresh(['employeeProfile.user', 'employeeProfile.department', 'employeeProfile.position', 'confirmer']);
            }

            $record->loadMissing('employeeProfile.user.roles');

            if (
                $record->employeeProfile?->user?->hasRole(AccessMatrix::ROLE_HR)
                && !$approver->hasRole(AccessMatrix::ROLE_ADMIN)
            ) {
                throw new \RuntimeException('Cham cong cua HR chi admin moi duoc duyet.');
            }

            $record->update([
                'is_confirmed' => true,
                'approval_status' => 'approved',
                'confirmed_by' => $approver->id,
                'rejected_by' => null,
                'confirmed_at' => now(self::TIMEZONE),
                'rejected_at' => null,
                'note' => $note ?: $record->note,
                'approval_note' => $note ?: $record->approval_note,
            ]);

            AttendanceApproval::query()->create([
                'attendance_record_id' => $record->id,
                'approval_type' => 'daily_confirmation',
                'approved_by' => $approver->id,
                'approved_at' => now(self::TIMEZONE),
                'status' => 'approved',
                'note' => $note,
            ]);

            $this->refreshMonthlySummary($record->fresh());
            $this->audit('attendance', 'confirm', "Confirm attendance record #{$record->id}", 'attendance_records', $record->id);

            return $record->fresh(['employeeProfile.user', 'employeeProfile.department', 'employeeProfile.position', 'confirmer']);
        });
    }

    public function rejectAttendance(AttendanceRecord $record, User $approver, ?string $note = null): AttendanceRecord
    {
        return $this->handleTransaction(function () use ($record, $approver, $note) {
            $record->update([
                'is_confirmed' => false,
                'approval_status' => 'rejected',
                'confirmed_by' => null,
                'rejected_by' => $approver->id,
                'confirmed_at' => null,
                'rejected_at' => now(self::TIMEZONE),
                'approval_note' => $note,
            ]);

            AttendanceApproval::query()->create([
                'attendance_record_id' => $record->id,
                'approval_type' => 'daily_confirmation',
                'approved_by' => $approver->id,
                'approved_at' => now(self::TIMEZONE),
                'status' => 'rejected',
                'note' => $note,
            ]);

            $this->audit('attendance', 'reject', "Reject attendance record #{$record->id}", 'attendance_records', $record->id);

            return $record->fresh(['employeeProfile.user', 'employeeProfile.department', 'employeeProfile.position', 'rejecter']);
        });
    }

    public function getMyAttendanceData(User $user, ?int $month = null, ?int $year = null): array
    {
        $month ??= (int) now(self::TIMEZONE)->month;
        $year ??= (int) now(self::TIMEZONE)->year;

        $profile = $user->employeeProfile;
        $records = collect();

        if ($profile) {
            $records = $this->baseRecordQuery()
                ->where('employee_profile_id', $profile->id)
                ->whereMonth('work_date', $month)
                ->whereYear('work_date', $year)
                ->orderByDesc('work_date')
                ->get();

            $records = $this->reconcileRecords($records);
        }

        return [
            'filters' => $this->buildFiltersPayload($month, $year),
            'summary' => $this->buildSummary($records),
            'records' => $records->map(fn (AttendanceRecord $record) => $this->transformRecord($record))->values(),
            'request_types' => $this->attendanceRequestTypes(),
        ];
    }

    public function getApprovalsData(array $filters = []): array
    {
        $query = $this->buildScopedQuery($this->sanitizeFilters($filters), true);
        $records = $this->reconcileRecords($query->orderBy('work_date')->get());

        return [
            'filters' => $this->buildFiltersPayload(
                (int) ($filters['month'] ?? now(self::TIMEZONE)->month),
                (int) ($filters['year'] ?? now(self::TIMEZONE)->year),
                $filters['employee_profile_id'] ?? null,
                $filters['keyword'] ?? null
            ),
            'summary' => $this->buildSummary($records),
            'records' => $records->map(fn (AttendanceRecord $record) => $this->transformRecord($record))->values(),
            'employees' => $this->employeeOptions(),
        ];
    }

    public function getReportData(User $user, array $filters = []): array
    {
        $filters = $this->sanitizeFilters($filters);
        $records = $this->reconcileRecords($this->buildScopedQuery($filters, false, $user)
            ->orderByDesc('work_date')
            ->orderBy('employee_profile_id')
            ->get());

        return [
            'filters' => $this->buildFiltersPayload(
                $filters['month'],
                $filters['year'],
                $filters['employee_profile_id'],
                $filters['keyword']
            ),
            'summary' => $this->buildSummary($records),
            'records' => $records->map(fn (AttendanceRecord $record) => $this->transformRecord($record))->values(),
            'employees' => $this->employeeOptions(),
            'month_lock' => $this->getMonthLockPayload($filters['month'], $filters['year']),
            'can_view_all' => $user->hasAnyRole([AccessMatrix::ROLE_ADMIN, AccessMatrix::ROLE_HR]),
        ];
    }

    public function submitAttendanceRequest(User $user, array $payload): AttendanceRequest|OvertimeRequest
    {
        return $this->handleTransaction(function () use ($user, $payload) {
            $profile = $user->employeeProfile;

            if (!$profile) {
                throw new \RuntimeException('Bạn chưa có hồ sơ nhân sự để gửi đơn chấm công.');
            }

            $requestType = (string) ($payload['request_type'] ?? '');
            $reason = trim((string) ($payload['reason'] ?? ''));

            if ($requestType === '') {
                throw new \RuntimeException('Vui lòng chọn loại đơn.');
            }

            if ($reason === '') {
                throw new \RuntimeException('Vui lòng nhập lý do.');
            }

            $approvalRequest = ApprovalRequest::query()->create([
                'request_type' => $requestType,
                'target_type' => AttendanceRequest::class,
                'target_id' => 0,
                'requested_by' => $user->id,
                'status' => 'pending',
                'submitted_at' => now(self::TIMEZONE),
                'reason' => $reason,
            ]);

            if ($requestType === 'overtime') {
                $startAt = Carbon::parse((string) ($payload['start_at'] ?? ''), self::TIMEZONE);
                $endAt = Carbon::parse((string) ($payload['end_at'] ?? ''), self::TIMEZONE);

                if ($endAt->lessThanOrEqualTo($startAt)) {
                    throw new \RuntimeException('Thời gian kết thúc tăng ca phải sau thời gian bắt đầu.');
                }

                $request = OvertimeRequest::query()->create([
                    'employee_profile_id' => $profile->id,
                    'approval_request_id' => $approvalRequest->id,
                    'work_date' => $startAt->toDateString(),
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'requested_minutes' => $this->calculateWorkedMinutes($startAt, $endAt),
                    'approved_minutes' => 0,
                    'status' => 'pending',
                    'reason' => $reason,
                    'requested_by' => $user->id,
                ]);

                $approvalRequest->update([
                    'target_type' => OvertimeRequest::class,
                    'target_id' => $request->id,
                ]);

                $this->audit('attendance', 'submit_overtime_request', "Submit overtime request #{$request->id}", 'overtime_requests', $request->id);

                return $request;
            }

            $request = AttendanceRequest::query()->create([
                'employee_profile_id' => $profile->id,
                'approval_request_id' => $approvalRequest->id,
                'request_type' => $requestType,
                'status' => 'pending',
                'request_date' => filled($payload['request_date'] ?? null) ? Carbon::parse((string) $payload['request_date'], self::TIMEZONE)->toDateString() : null,
                'from_date' => filled($payload['from_date'] ?? null) ? Carbon::parse((string) $payload['from_date'], self::TIMEZONE)->toDateString() : null,
                'to_date' => filled($payload['to_date'] ?? null) ? Carbon::parse((string) $payload['to_date'], self::TIMEZONE)->toDateString() : null,
                'from_time' => filled($payload['from_time'] ?? null) ? $payload['from_time'] : null,
                'to_time' => filled($payload['to_time'] ?? null) ? $payload['to_time'] : null,
                'leave_type' => $requestType === 'leave' ? (($payload['leave_type'] ?? 'paid') === 'unpaid' ? 'unpaid' : 'paid') : null,
                'requested_status' => $payload['requested_status'] ?? null,
                'reason' => $reason,
            ]);

            $approvalRequest->update([
                'target_type' => AttendanceRequest::class,
                'target_id' => $request->id,
            ]);

            $this->audit('attendance', 'submit_request', "Submit attendance request #{$request->id}", 'attendance_requests', $request->id);

            return $request;
        });
    }

    public function lockMonth(User $actor, int $month, int $year, ?string $note = null): AttendanceMonthLock
    {
        return $this->handleTransaction(function () use ($actor, $month, $year, $note) {
            $lock = AttendanceMonthLock::query()->updateOrCreate(
                [
                    'month' => $month,
                    'year' => $year,
                    'department_id' => null,
                ],
                [
                    'is_locked' => true,
                    'locked_at' => now(self::TIMEZONE),
                    'locked_by' => $actor->id,
                    'unlocked_at' => null,
                    'unlocked_by' => null,
                    'note' => $note,
                ]
            );

            $this->audit('attendance', 'lock_month', "Lock attendance month {$month}/{$year}", 'attendance_month_locks', $lock->id);

            return $lock;
        });
    }

    public function unlockMonth(User $actor, int $month, int $year, ?string $note = null): AttendanceMonthLock
    {
        return $this->handleTransaction(function () use ($actor, $month, $year, $note) {
            $lock = AttendanceMonthLock::query()->firstOrCreate(
                [
                    'month' => $month,
                    'year' => $year,
                    'department_id' => null,
                ]
            );

            $lock->update([
                'is_locked' => false,
                'unlocked_at' => now(self::TIMEZONE),
                'unlocked_by' => $actor->id,
                'note' => $note,
            ]);

            $this->audit('attendance', 'unlock_month', "Unlock attendance month {$month}/{$year}", 'attendance_month_locks', $lock->id);

            return $lock;
        });
    }

    public function exportExcelHtml(User $user, array $filters = []): array
    {
        $report = $this->getReportData($user, $filters);
        $filename = sprintf(
            'attendance-report-%04d-%02d.xls',
            $report['filters']['year'],
            $report['filters']['month']
        );

        $this->logExport($user, 'excel', $report['filters']);

        return [
            'filename' => $filename,
            'content' => view('exports.attendance-report-excel', [
                'summary' => $report['summary'],
                'records' => $report['records'],
                'filters' => $report['filters'],
            ])->render(),
        ];
    }

    public function exportPdfHtml(User $user, array $filters = []): array
    {
        $report = $this->getReportData($user, $filters);
        $filename = sprintf(
            'attendance-report-%04d-%02d.pdf',
            $report['filters']['year'],
            $report['filters']['month']
        );

        $this->logExport($user, 'pdf', $report['filters']);

        return [
            'filename' => $filename,
            'html' => view('exports.attendance-report-pdf', [
                'summary' => $report['summary'],
                'records' => $report['records'],
                'filters' => $report['filters'],
            ])->render(),
        ];
    }

    public function markAbsencesForDate(Carbon|string|null $date = null): int
    {
        $workDate = $date instanceof Carbon
            ? $date->copy()->timezone(self::TIMEZONE)->startOfDay()
            : Carbon::parse($date ?? now(self::TIMEZONE)->toDateString(), self::TIMEZONE)->startOfDay();

        if ($workDate->isWeekend()) {
            return 0;
        }

        if (Holiday::query()->whereDate('holiday_date', $workDate->toDateString())->exists()) {
            return 0;
        }

        $profiles = EmployeeProfile::query()
            ->whereIn('employment_status', ['active', 'probation'])
            ->whereDoesntHave('attendanceRecords', function (Builder $query) use ($workDate) {
                $query->whereDate('work_date', $workDate->toDateString());
            })
            ->with('user:id,status')
            ->get()
            ->filter(fn (EmployeeProfile $profile) => $profile->user?->status === 'active');

        foreach ($profiles as $profile) {
            $record = AttendanceRecord::query()->create([
                'employee_profile_id' => $profile->id,
                'work_date' => $workDate->toDateString(),
                'attendance_status' => 'absent',
                'approval_status' => 'pending',
                'day_status' => 'absent',
                'worked_minutes' => 0,
                'is_confirmed' => false,
                'missing_check_in' => false,
                'missing_check_out' => false,
                'note' => 'Auto marked absent',
            ]);

            $this->refreshMonthlySummary($record);
            $this->audit('attendance', 'mark_absent', "Auto mark absent attendance record #{$record->id}", 'attendance_records', $record->id);
        }

        return $profiles->count();
    }

    private function buildScopedQuery(array $filters, bool $pendingOnly = false, ?User $actor = null): Builder
    {
        $actor ??= $this->user();
        $query = $this->baseRecordQuery()
            ->whereMonth('work_date', $filters['month'])
            ->whereYear('work_date', $filters['year']);

        if ($pendingOnly) {
            $query->where('approval_status', 'pending');
        }

        if ($filters['employee_profile_id']) {
            $query->where('employee_profile_id', $filters['employee_profile_id']);
        }

        if ($filters['keyword']) {
            $keyword = $filters['keyword'];
            $query->where(function (Builder $builder) use ($keyword) {
                $builder
                    ->whereHas('employeeProfile.user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$keyword}%"))
                    ->orWhereHas('employeeProfile', fn (Builder $profileQuery) => $profileQuery->where('employee_code', 'like', "%{$keyword}%"))
                    ->orWhere('attendance_status', 'like', "%{$keyword}%");
            });
        }

        if ($actor && !$actor->hasAnyRole([AccessMatrix::ROLE_ADMIN, AccessMatrix::ROLE_HR])) {
            $query->where('employee_profile_id', $actor->employeeProfile?->id ?? 0);
        }

        return $query;
    }

    private function sanitizeFilters(array $filters): array
    {
        $now = now(self::TIMEZONE);

        return [
            'month' => max(1, min(12, (int) ($filters['month'] ?? $now->month))),
            'year' => max(2000, min(2100, (int) ($filters['year'] ?? $now->year))),
            'employee_profile_id' => filled($filters['employee_profile_id'] ?? null) ? (int) $filters['employee_profile_id'] : null,
            'keyword' => trim((string) ($filters['keyword'] ?? '')),
        ];
    }

    private function buildSummary(Collection $records): array
    {
        $normalizedStatuses = $records->map(fn (AttendanceRecord $record) => $this->normalizeAttendanceStatus($record->attendance_status));
        $resolvedMinutes = $records->sum(fn (AttendanceRecord $record) => $this->resolveWorkedMinutes($record));
        $totalMinutes = (int) $resolvedMinutes;
        $confirmedCount = (int) $records->where('approval_status', 'approved')->count();
        $rejectedCount = (int) $records->where('approval_status', 'rejected')->count();
        $lateCount = (int) $normalizedStatuses->filter(fn (string $status) => $status === 'late')->count();
        $onTimeCount = (int) $normalizedStatuses->filter(fn (string $status) => $status === 'on_time')->count();
        $presentCount = (int) $normalizedStatuses->filter(fn (string $status) => in_array($status, ['on_time', 'late'], true))->count();
        $absentCount = (int) $normalizedStatuses->filter(fn (string $status) => $status === 'absent')->count();
        $earlyLeaveCount = (int) $records->filter(fn (AttendanceRecord $record) => ($record->day_status ?? null) === 'early_leave')->count();

        return [
            'total_records' => $records->count(),
            'confirmed_records' => $confirmedCount,
            'pending_records' => (int) $records->where('approval_status', 'pending')->count(),
            'rejected_records' => $rejectedCount,
            'present_records' => $presentCount,
            'on_time_records' => $onTimeCount,
            'late_records' => $lateCount,
            'absent_records' => $absentCount,
            'early_leave_records' => $earlyLeaveCount,
            'total_worked_minutes' => $totalMinutes,
            'total_worked_hours' => round($totalMinutes / 60, 2),
        ];
    }

    private function transformRecord(AttendanceRecord $record): array
    {
        $normalizedStatus = $this->normalizeAttendanceStatus($record->attendance_status);
        $resolvedWorkedMinutes = $this->resolveWorkedMinutes($record);

        return [
            'id' => $record->id,
            'employee_profile_id' => $record->employee_profile_id,
            'employee_name' => $record->employeeProfile?->user?->name,
            'employee_code' => $record->employeeProfile?->employee_code,
            'employee_role' => AccessMatrix::primaryRole($record->employeeProfile?->user),
            'department_name' => $record->employeeProfile?->department?->name,
            'position_name' => $record->employeeProfile?->position?->name,
            'work_date' => optional($record->work_date)->format('Y-m-d'),
            'check_in_at' => optional($record->check_in_at)->format('Y-m-d H:i:s'),
            'check_out_at' => optional($record->check_out_at)->format('Y-m-d H:i:s'),
            'check_in_ip_address' => $record->check_in_ip_address,
            'check_out_ip_address' => $record->check_out_ip_address,
            'check_in_device' => $record->check_in_device,
            'check_out_device' => $record->check_out_device,
            'worked_minutes' => $resolvedWorkedMinutes,
            'late_minutes' => (int) $record->late_minutes,
            'early_leave_minutes' => (int) $record->early_leave_minutes,
            'overtime_minutes' => (int) $record->overtime_minutes,
            'missing_check_in' => (bool) $record->missing_check_in,
            'missing_check_out' => (bool) $record->missing_check_out,
            'attendance_status' => $normalizedStatus,
            'approval_status' => $record->approval_status ?: ($record->is_confirmed ? 'approved' : 'pending'),
            'day_status' => $record->day_status ?: $this->normalizeDayStatus($record),
            'is_confirmed' => (bool) $record->is_confirmed,
            'confirmed_at' => optional($record->confirmed_at)->format('Y-m-d H:i:s'),
            'confirmed_by_name' => $record->confirmer?->name,
            'note' => $record->note,
            'approval_note' => $record->approval_note,
        ];
    }

    private function employeeOptions(): array
    {
        return EmployeeProfile::query()
            ->with(['user:id,name', 'department:id,name'])
            ->orderBy('employee_code')
            ->get()
            ->map(fn (EmployeeProfile $profile) => [
                'id' => $profile->id,
                'label' => trim(($profile->employee_code ?: 'EMP') . ' - ' . ($profile->user?->name ?: 'Unknown')),
                'department_name' => $profile->department?->name,
            ])
            ->values()
            ->all();
    }

    private function baseRecordQuery(): Builder
    {
        return AttendanceRecord::query()->with([
            'employeeProfile.user:id,name',
            'employeeProfile.department:id,name',
            'employeeProfile.position:id,name',
            'workShift:id,shift_name,start_time,end_time,standard_minutes,grace_minutes,late_grace_minutes,early_leave_grace_minutes',
            'confirmer:id,name',
            'rejecter:id,name',
        ]);
    }

    private function determineAttendanceStatus(Carbon|string $checkInAt): string
    {
        return $this->determineLateMinutes(
            $checkInAt instanceof Carbon ? $checkInAt->copy() : Carbon::parse($checkInAt, self::TIMEZONE),
            null
        ) > 0 ? 'late' : 'on_time';
    }

    private function reconcileRecords(Collection $records): Collection
    {
        return $records->map(function (AttendanceRecord $record) {
            $resolvedWorkedMinutes = $this->resolveWorkedMinutes($record);
            $normalizedStatus = $record->check_in_at
                ? $this->determineAttendanceStatus($record->check_in_at)
                : $this->normalizeAttendanceStatus($record->attendance_status);
            $dayStatus = $this->normalizeDayStatus($record);
            $lateMinutes = $record->check_in_at ? $this->determineLateMinutes(Carbon::parse($record->check_in_at, self::TIMEZONE), $record->workShift) : 0;
            $earlyLeaveMinutes = $record->check_out_at ? $this->determineEarlyLeaveMinutes(Carbon::parse($record->check_out_at, self::TIMEZONE), $record->workShift) : 0;

            $dirty = false;

            if ((int) $record->worked_minutes !== $resolvedWorkedMinutes) {
                $record->worked_minutes = $resolvedWorkedMinutes;
                $dirty = true;
            }

            if ((string) $record->attendance_status !== $normalizedStatus) {
                $record->attendance_status = $normalizedStatus;
                $dirty = true;
            }

            if ((string) ($record->day_status ?? '') !== $dayStatus) {
                $record->day_status = $dayStatus;
                $dirty = true;
            }

            if ((int) ($record->late_minutes ?? 0) !== $lateMinutes) {
                $record->late_minutes = $lateMinutes;
                $dirty = true;
            }

            if ((int) ($record->early_leave_minutes ?? 0) !== $earlyLeaveMinutes) {
                $record->early_leave_minutes = $earlyLeaveMinutes;
                $dirty = true;
            }

            if ($dirty) {
                $record->saveQuietly();
            }

            return $record;
        });
    }

    private function normalizeAttendanceStatus(?string $status): string
    {
        return match ($status) {
            'on_time', 'late', 'absent' => $status,
            'present' => 'on_time',
            'pending', 'leave' => 'absent',
            'half_day' => 'late',
            default => 'absent',
        };
    }

    private function resolveWorkedMinutes(AttendanceRecord $record): int
    {
        if ($record->check_in_at && $record->check_out_at) {
            return $this->calculateWorkedMinutes(
                Carbon::parse($record->check_in_at, self::TIMEZONE),
                Carbon::parse($record->check_out_at, self::TIMEZONE)
            );
        }

        return max(0, (int) $record->worked_minutes);
    }

    private function normalizeDayStatus(AttendanceRecord $record): string
    {
        if ($record->missing_check_in) {
            return 'missing_check_in';
        }

        if ($record->missing_check_out || ($record->check_in_at && !$record->check_out_at)) {
            return 'missing_check_out';
        }

        if ($record->attendance_status === 'absent' && !$record->check_in_at) {
            return 'absent';
        }

        if (($record->early_leave_minutes ?? 0) > 0) {
            return 'early_leave';
        }

        if (($record->late_minutes ?? 0) > 0 || $this->normalizeAttendanceStatus($record->attendance_status) === 'late') {
            return 'late';
        }

        return 'present';
    }

    private function calculateWorkedMinutes(Carbon $checkInAt, Carbon $checkOutAt): int
    {
        return (int) round(abs($checkInAt->diffInMinutes($checkOutAt, false)));
    }

    private function refreshMonthlySummary(AttendanceRecord $record): void
    {
        $month = (int) Carbon::parse($record->work_date, self::TIMEZONE)->month;
        $year = (int) Carbon::parse($record->work_date, self::TIMEZONE)->year;

        $records = AttendanceRecord::query()
            ->where('employee_profile_id', $record->employee_profile_id)
            ->whereMonth('work_date', $month)
            ->whereYear('work_date', $year)
            ->get();

        AttendanceMonthlySummary::query()->updateOrCreate(
            [
                'employee_profile_id' => $record->employee_profile_id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'total_working_days' => (int) $records->count(),
                'total_present_days' => (float) $records->filter(fn (AttendanceRecord $item) => in_array($this->normalizeAttendanceStatus($item->attendance_status), ['on_time', 'late'], true))->count(),
                'total_absent_days' => (float) $records->filter(fn (AttendanceRecord $item) => $this->normalizeAttendanceStatus($item->attendance_status) === 'absent')->count(),
                'total_leave_days' => (int) $records->filter(fn (AttendanceRecord $item) => ($item->day_status ?? null) === 'leave')->count(),
                'total_unpaid_leave_days' => (int) $records->filter(fn (AttendanceRecord $item) => ($item->day_status ?? null) === 'unpaid_leave')->count(),
                'total_business_trip_days' => (int) $records->filter(fn (AttendanceRecord $item) => ($item->day_status ?? null) === 'business_trip')->count(),
                'total_late_count' => (int) $records->filter(fn (AttendanceRecord $item) => $this->normalizeAttendanceStatus($item->attendance_status) === 'late')->count(),
                'total_early_leave_count' => (int) $records->filter(fn (AttendanceRecord $item) => ($item->day_status ?? null) === 'early_leave')->count(),
                'total_overtime_minutes' => (int) $records->sum(fn (AttendanceRecord $item) => (int) ($item->overtime_minutes ?? 0)),
            ]
        );
    }

    private function notifyHr(AttendanceRecord $record, string $eventType): void
    {
        $hrUserIds = User::query()
            ->role(AccessMatrix::ROLE_HR)
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        if (empty($hrUserIds)) {
            return;
        }

        $employeeName = $record->employeeProfile?->user?->name ?: 'Nhan vien';
        $time = $eventType === 'check_in' ? $record->check_in_at : $record->check_out_at;
        $formattedTime = optional($time)->format('d/m/Y H:i');
        $actionLabel = $eventType === 'check_in' ? 'check-in' : 'check-out';

        $this->notificationService->createForUsers(
            $hrUserIds,
            'Thong bao cham cong',
            "{$employeeName} da {$actionLabel} luc {$formattedTime}.",
            [
                'attendance_record_id' => $record->id,
                'event_type' => $eventType,
            ],
            '/attendance/approvals',
            null,
            'attendance',
            $this->user()?->getAuthIdentifier(),
            AttendanceRecord::class,
            $record->id
        );
    }

    private function normalizeEventNote(string $prefix, ?string $userAgent): string
    {
        if (!$userAgent) {
            return $prefix . ' via web';
        }

        return $prefix . ' via web - ' . mb_substr($userAgent, 0, 200);
    }

    private function logExport(User $user, string $fileType, array $filters): void
    {
        ExportHistory::query()->create([
            'user_id' => $user->id,
            'module' => 'attendance',
            'file_type' => $fileType,
            'filter_data' => $filters,
            'file_path' => null,
            'exported_at' => now(self::TIMEZONE),
        ]);

        $this->audit('attendance', 'export_' . $fileType, 'Export attendance report', 'export_histories', null);
    }

    private function buildFiltersPayload(int $month, int $year, ?int $employeeProfileId = null, ?string $keyword = null): array
    {
        return [
            'month' => $month,
            'year' => $year,
            'employee_profile_id' => $employeeProfileId,
            'keyword' => $keyword ?? '',
            'months' => collect(range(1, 12))->map(fn (int $value) => [
                'value' => $value,
                'label' => sprintf('Thang %02d', $value),
            ])->all(),
            'years' => collect(range((int) now(self::TIMEZONE)->year - 3, (int) now(self::TIMEZONE)->year + 1))
                ->map(fn (int $value) => ['value' => $value, 'label' => (string) $value])
                ->all(),
        ];
    }

    private function attendanceRequestTypes(): array
    {
        return [
            ['value' => 'leave', 'label' => 'Xin nghỉ phép'],
            ['value' => 'late_early', 'label' => 'Xin đi muộn / về sớm'],
            ['value' => 'forgot_check', 'label' => 'Xin quên chấm công'],
            ['value' => 'business_trip', 'label' => 'Xin công tác'],
            ['value' => 'make_up', 'label' => 'Xin làm bù'],
            ['value' => 'overtime', 'label' => 'Đăng ký tăng ca'],
        ];
    }

    private function getMonthLockPayload(int $month, int $year): array
    {
        $lock = AttendanceMonthLock::query()
            ->where('month', $month)
            ->where('year', $year)
            ->whereNull('department_id')
            ->latest('id')
            ->first();

        return [
            'is_locked' => (bool) $lock?->is_locked,
            'locked_at' => optional($lock?->locked_at)->format('Y-m-d H:i:s'),
            'locked_by' => $lock?->locked_by,
            'note' => $lock?->note,
        ];
    }

    private function resolveWorkShift(EmployeeProfile $profile, Carbon $workDate): ?WorkShift
    {
        $assignment = EmployeeWorkShiftAssignment::query()
            ->with('workShift')
            ->where('is_active', true)
            ->where(function (Builder $query) use ($profile) {
                $query->where('employee_profile_id', $profile->id);

                if ($profile->department_id) {
                    $query->orWhere(function (Builder $departmentQuery) use ($profile) {
                        $departmentQuery
                            ->whereNull('employee_profile_id')
                            ->where('department_id', $profile->department_id);
                    });
                }
            })
            ->whereDate('effective_from', '<=', $workDate->toDateString())
            ->where(function (Builder $query) use ($workDate) {
                $query
                    ->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $workDate->toDateString());
            })
            ->orderByDesc('employee_profile_id')
            ->orderByDesc('effective_from')
            ->first();

        return $assignment?->workShift ?: $profile->defaultWorkShift;
    }

    private function buildShiftSnapshot(?WorkShift $workShift): ?array
    {
        if (!$workShift) {
            return null;
        }

        return [
            'id' => $workShift->id,
            'shift_name' => $workShift->shift_name,
            'start_time' => $workShift->start_time,
            'end_time' => $workShift->end_time,
            'standard_minutes' => $workShift->standard_minutes,
            'grace_minutes' => $workShift->grace_minutes,
            'late_grace_minutes' => $workShift->late_grace_minutes,
            'early_leave_grace_minutes' => $workShift->early_leave_grace_minutes,
        ];
    }

    private function determineLateMinutes(Carbon $checkInAt, ?WorkShift $workShift): int
    {
        [$expectedStart] = $this->shiftBoundaries($checkInAt, $workShift);
        $allowedMinutes = (int) ($workShift?->late_grace_minutes ?? $workShift?->grace_minutes ?? 0);
        $diffMinutes = (int) $expectedStart->diffInMinutes($checkInAt, false);

        return max(0, $diffMinutes - $allowedMinutes);
    }

    private function determineEarlyLeaveMinutes(Carbon $checkOutAt, ?WorkShift $workShift): int
    {
        [, $expectedEnd] = $this->shiftBoundaries($checkOutAt, $workShift);
        $allowedMinutes = (int) ($workShift?->early_leave_grace_minutes ?? $workShift?->grace_minutes ?? 0);
        $diffMinutes = (int) $checkOutAt->diffInMinutes($expectedEnd, false);

        return $diffMinutes < 0 ? max(0, abs($diffMinutes) - $allowedMinutes) : 0;
    }

    private function determineOvertimeMinutes(int $workedMinutes, ?WorkShift $workShift): int
    {
        $standardMinutes = (int) ($workShift?->standard_minutes ?? 0);

        return max(0, $workedMinutes - $standardMinutes);
    }

    private function determineDayStatus(Carbon $checkInAt, Carbon $checkOutAt, ?WorkShift $workShift): string
    {
        if ($this->determineEarlyLeaveMinutes($checkOutAt, $workShift) > 0) {
            return 'early_leave';
        }

        if ($this->determineLateMinutes($checkInAt, $workShift) > 0) {
            return 'late';
        }

        return 'present';
    }

    private function shiftBoundaries(Carbon $dateTime, ?WorkShift $workShift): array
    {
        $startTime = $workShift?->start_time ?? sprintf('%02d:%02d:00', self::WORK_START_HOUR, self::WORK_START_MINUTE);
        $endTime = $workShift?->end_time ?? '17:00:00';
        [$startHour, $startMinute] = array_map('intval', explode(':', substr((string) $startTime, 0, 5)));
        [$endHour, $endMinute] = array_map('intval', explode(':', substr((string) $endTime, 0, 5)));

        return [
            $dateTime->copy()->startOfDay()->setTime($startHour, $startMinute),
            $dateTime->copy()->startOfDay()->setTime($endHour, $endMinute),
        ];
    }

    private function ensureMonthNotLocked(?EmployeeProfile $profile, Carbon $workDate): void
    {
        if (!$profile) {
            return;
        }

        $isLocked = AttendanceMonthLock::query()
            ->where('month', (int) $workDate->month)
            ->where('year', (int) $workDate->year)
            ->where('is_locked', true)
            ->where(function (Builder $query) use ($profile) {
                $query->whereNull('department_id');

                if ($profile->department_id) {
                    $query->orWhere('department_id', $profile->department_id);
                }
            })
            ->exists();

        if ($isLocked) {
            throw new \RuntimeException('Bang cong thang nay da bi khoa, khong the chinh sua.');
        }
    }
}
