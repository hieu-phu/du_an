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
use App\Models\AttendanceAdjustment;
use App\Models\EmployeeProfile;
use App\Models\EmployeeWorkShiftAssignment;
use App\Models\ExportHistory;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Models\WorkShift;
use App\Repositories\AttendanceRepository;
use App\Support\AccessMatrix;
use App\Support\PositionCapability;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
class AttendanceService extends BaseService
{
    private const TIMEZONE = 'Asia/Ho_Chi_Minh';
    private const WORK_START_HOUR = 8;
    private const WORK_START_MINUTE = 0;
    private const WORK_END_HOUR = 17;
    private const WORK_END_MINUTE = 30;
    private const DEFAULT_LATE_GRACE_MINUTES = 10;
    private const DEFAULT_EARLY_LEAVE_GRACE_MINUTES = 5;
    private const FULL_WORK_UNIT_MINUTES = 480; // 8h
    private const HALF_WORK_UNIT_MINUTES = 240; // 4h

    public function __construct(
        protected AttendanceRepository $attendanceRepository,
        protected NotificationService $notificationService,
        protected LeaveManagementService $leaveService
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
            $workShift = $record->workShift ?: $this->resolveWorkShift($record->employeeProfile, $now);
            $workedMinutes = $this->calculateWorkedMinutes($checkInTime, $now, $this->shiftConfigFromWorkShift($workShift));
            $lateMinutes = $this->determineLateMinutes($checkInTime, $workShift);
            $earlyLeaveMinutes = $this->determineEarlyLeaveMinutes($now, $workShift);
            $overtimeMinutes = $this->resolveApprovedOvertimeMinutesForDate($employeeProfileId, $today);
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
                'approval_status' => 'pending',
                'is_confirmed' => false,
                'confirmed_by' => null,
                'confirmed_at' => null,
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

    public function confirmAttendance(AttendanceRecord $record, User $approver, ?string $note = null, ?string $resolvedCheckOutTime = null): AttendanceRecord
    {
        return $this->handleTransaction(function () use ($record, $approver, $note, $resolvedCheckOutTime) {
            $record->loadMissing(['employeeProfile.user', 'employeeProfile.position', 'workShift']);
            $this->ensureReviewerOutranksEmployee($approver, $record->employeeProfile);

            $workDate = Carbon::parse($record->work_date, self::TIMEZONE)->toDateString();
            if (!$record->check_out_at && $record->check_in_at && filled($resolvedCheckOutTime)) {
                $resolvedCheckIn = Carbon::parse($record->check_in_at, self::TIMEZONE);
                $resolvedCheckOut = $this->resolveTimeForRecordDate($record, $resolvedCheckOutTime);

                if ($resolvedCheckOut->lessThanOrEqualTo($resolvedCheckIn)) {
                    throw new \RuntimeException('Gio check-out bo sung phai sau gio check-in.');
                }

                [$attendanceStatus, $dayStatus, $workedMinutes, $lateMinutes, $earlyLeaveMinutes, $overtimeMinutes] =
                    $this->previewAttendanceMetrics($record, $resolvedCheckIn, $resolvedCheckOut);

                $record->forceFill([
                    'check_out_at' => $resolvedCheckOut,
                    'worked_minutes' => $workedMinutes,
                    'late_minutes' => $lateMinutes,
                    'early_leave_minutes' => $earlyLeaveMinutes,
                    'overtime_minutes' => $overtimeMinutes,
                    'missing_check_out' => false,
                    'attendance_status' => $attendanceStatus,
                    'day_status' => $dayStatus,
                    'note' => trim(collect(array_filter([
                        $record->note,
                        'Admin resolved missing check-out at ' . $resolvedCheckOut->format('H:i'),
                    ]))->implode(' | ')),
                ])->save();

                AttendanceEvent::query()->create([
                    'attendance_record_id' => $record->id,
                    'employee_profile_id' => $record->employee_profile_id,
                    'event_type' => 'manual_update',
                    'event_time' => $resolvedCheckOut,
                    'note' => 'Admin resolved missing check-out before approval',
                    'created_by' => $approver->id,
                ]);

                $record->refresh();
            }

            $lateMinutes = $record->check_in_at
                ? $this->determineLateMinutes(Carbon::parse($record->check_in_at, self::TIMEZONE), $record->workShift)
                : (int) ($record->late_minutes ?? 0);
            $earlyLeaveMinutes = $record->check_out_at
                ? $this->determineEarlyLeaveMinutes(Carbon::parse($record->check_out_at, self::TIMEZONE), $record->workShift)
                : (int) ($record->early_leave_minutes ?? 0);
            $requiresApprovedRequest = $this->requiresApprovedRequestForConfirmation($record, $lateMinutes, $earlyLeaveMinutes);
            $approvedRequest = $requiresApprovedRequest
                ? $this->findApprovedAttendanceRequestForDate((int) $record->employee_profile_id, $workDate)
                : null;

            if ($requiresApprovedRequest && !$approvedRequest) {
                $record->update([
                    'is_confirmed' => false,
                    'approval_status' => 'pending',
                    'confirmed_by' => null,
                    'confirmed_at' => null,
                ]);

                throw new \RuntimeException('Ban ghi thieu check-in/check-out, di muon hoac ve som chi duoc duyet khi da co don cham cong duoc phe duyet hoac admin bo sung gio ra hop le.');
            }

            if ($record->is_confirmed) {
                if (($record->approval_status ?? 'pending') !== 'approved') {
                    $record->update([
                        'approval_status' => 'approved',
                    ]);
                }
                return $record->fresh(['employeeProfile.user', 'employeeProfile.department', 'employeeProfile.position', 'confirmer']);
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
            $record->loadMissing(['employeeProfile.user', 'employeeProfile.position']);
            $this->ensureReviewerOutranksEmployee($approver, $record->employeeProfile);

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

        $summary = $this->buildSummary($records);

        if ($profile) {
            $summary = array_merge($summary, $this->buildMyRequestSummary((int) $profile->id));
        }

        return [
            'filters' => $this->buildFiltersPayload($month, $year),
            'summary' => $summary,
            'records' => $records->map(fn (AttendanceRecord $record) => $this->transformRecord($record))->values(),
            'request_types' => $this->attendanceRequestTypes(),
            'recent_requests' => $this->getMyRequestHistory($user),
            'overtime_catalog' => $profile ? $this->buildOvertimeCatalogPayload($profile, now(self::TIMEZONE)) : null,
            'leave_types' => $this->leaveService->leaveTypeOptions(true),
            'leave_balances' => $profile ? $this->getLeaveBalancePayload($profile, $year) : [],
        ];
    }

    public function getApprovalsData(array $filters = [], ?User $viewer = null): array
    {
        $viewer ??= $this->user();
        $sanitizedFilters = $this->sanitizeFilters($filters);
        $onlyLeave = (bool) ($filters['only_leave'] ?? false);
        $allRecords = collect();
        $records = collect();
        $reviewedRecords = collect();

        if (!$onlyLeave) {
            $query = $this->buildScopedQuery($sanitizedFilters, false, $viewer);
            $this->applyReviewerVisibilityToRecordQuery($query, $viewer);

            $allRecords = $this->reconcileRecords($query->orderBy('work_date')->get());
            $records = $allRecords
                ->filter(fn (AttendanceRecord $record) => ($record->approval_status ?? 'pending') === 'pending' && !(bool) $record->is_confirmed)
                ->values();
            $reviewedRecords = $allRecords
                ->filter(fn (AttendanceRecord $record) => in_array((string) ($record->approval_status ?? ''), ['approved', 'rejected'], true))
                ->sortByDesc(fn (AttendanceRecord $record) => optional($record->confirmed_at ?? $record->rejected_at ?? $record->updated_at)->timestamp)
                ->take(200)
                ->values();
        }

        return [
            'approval_mode' => $onlyLeave ? 'leave' : 'attendance',
            'filters' => $this->buildFiltersPayload(
                $sanitizedFilters['month'],
                $sanitizedFilters['year'],
                $sanitizedFilters['employee_profile_id'],
                $sanitizedFilters['keyword']
            ),
            'summary' => $this->buildSummary($records),
            'approval_summary' => [
                'pending_records' => $records->count(),
                'approved_records' => $allRecords->where('approval_status', 'approved')->count(),
                'rejected_records' => $allRecords->where('approval_status', 'rejected')->count(),
            ],
            'records' => $records->map(fn (AttendanceRecord $record) => $this->transformRecord($record))->values(),
            'reviewed_records' => $reviewedRecords->map(fn (AttendanceRecord $record) => $this->transformRecord($record))->values(),
            'employees' => $this->employeeOptionsForReviewer($viewer),
            'request_approvals' => $this->getPendingApprovalRequests([...$sanitizedFilters, 'only_leave' => $onlyLeave], $viewer),
            'reviewed_request_approvals' => $this->getReviewedApprovalRequests([...$sanitizedFilters, 'only_leave' => $onlyLeave], $viewer),
        ];
    }

    public function getMyAdjustmentData(User $user): array
    {
        $profile = $user->employeeProfile;
        $records = [];
        $adjustments = [];

        if ($profile) {
            $records = $this->baseRecordQuery()
                ->where('employee_profile_id', $profile->id)
                ->where(function (Builder $query) {
                    $query
                        ->whereNull('approval_status')
                        ->orWhere('approval_status', '!=', 'approved');
                })
                ->where(function (Builder $query) {
                    $query
                        ->whereNull('is_confirmed')
                        ->orWhere('is_confirmed', false);
                })
                ->orderByDesc('work_date')
                ->limit(60)
                ->get()
                ->map(fn (AttendanceRecord $record) => $this->transformRecord($record))
                ->values()
                ->all();

            $adjustments = AttendanceAdjustment::query()
                ->with([
                    'attendanceRecord.employeeProfile.user:id,name',
                    'attendanceRecord.employeeProfile.department:id,name',
                    'approvalRequest.reviewer:id,name',
                ])
                ->whereHas('attendanceRecord', fn (Builder $query) => $query->where('employee_profile_id', $profile->id))
                ->latest('id')
                ->limit(50)
                ->get()
                ->map(fn (AttendanceAdjustment $item) => $this->transformAdjustment($item))
                ->values()
                ->all();
        }

        return [
            'records' => $records,
            'adjustments' => $adjustments,
        ];
    }

    public function getAdjustmentApprovalsData(array $filters = []): array
    {
        $month = max(1, min(12, (int) ($filters['month'] ?? now(self::TIMEZONE)->month)));
        $year = max(2000, min(2100, (int) ($filters['year'] ?? now(self::TIMEZONE)->year)));

        $baseQuery = AttendanceAdjustment::query()
            ->with([
                'attendanceRecord.employeeProfile.user:id,name',
                'attendanceRecord.employeeProfile.department:id,name',
                'attendanceRecord.employeeProfile.position:id,name',
                'approvalRequest:id,status,submitted_at,reviewed_at,reviewed_by,review_note',
                'approvalRequest.reviewer:id,name',
            ])
            ->whereHas('attendanceRecord', function (Builder $recordQuery) use ($month, $year) {
                $recordQuery->whereMonth('work_date', $month)->whereYear('work_date', $year);
            });

        if (filled($filters['employee_profile_id'] ?? null)) {
            $baseQuery->whereHas('attendanceRecord', fn (Builder $recordQuery) => $recordQuery->where('employee_profile_id', (int) $filters['employee_profile_id']));
        }

        $pendingAdjustments = (clone $baseQuery)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(fn (AttendanceAdjustment $item) => $this->transformAdjustment($item))
            ->values()
            ->all();

        $reviewedAdjustments = (clone $baseQuery)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(fn (AttendanceAdjustment $item) => $this->transformAdjustment($item))
            ->values()
            ->all();

        return [
            'filters' => $this->buildFiltersPayload($month, $year, filled($filters['employee_profile_id'] ?? null) ? (int) $filters['employee_profile_id'] : null),
            'employees' => $this->employeeOptions(),
            'adjustments' => $pendingAdjustments,
            'reviewed_adjustments' => $reviewedAdjustments,
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
            'overtime_details' => $this->getOvertimeDetails($user, $filters),
            'employees' => $this->employeeOptions(),
            'month_lock' => $this->getMonthLockPayload($filters['month'], $filters['year']),
            'can_view_all' => AccessMatrix::canManageAllAttendance($user),
        ];
    }
    public function submitAttendanceRequest(User $user, array $payload): AttendanceRequest|OvertimeRequest
    {
        return $this->handleTransaction(function () use ($user, $payload) {
            $profile = $user->employeeProfile;

            if (!$profile) {
                throw new \RuntimeException('Ban chua co ho so nhan su de gui don cham cong.');
            }

            $requestType = (string) ($payload['request_type'] ?? '');
            $reason = trim((string) ($payload['reason'] ?? ''));

            if ($requestType === '') {
                throw new \RuntimeException('Vui long chon loai don.');
            }

            if ($reason === '') {
                throw new \RuntimeException('Vui long nhap ly do.');
            }

            $normalizedAttendance = null;
            $normalizedOvertime = null;

            if ($requestType === 'overtime') {
                $normalizedOvertime = $this->validateOvertimeRequestOnSubmit($profile, $payload);
            } else {
                $normalizedAttendance = $this->validateAttendanceRequestOnSubmit($profile, $requestType, $payload);
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
                $startAt = $normalizedOvertime['start_at'];
                $endAt = $normalizedOvertime['end_at'];

                $request = OvertimeRequest::query()->create([
                    'employee_profile_id' => $profile->id,
                    'approval_request_id' => $approvalRequest->id,
                    'work_date' => $startAt->toDateString(),
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'requested_minutes' => $normalizedOvertime['requested_minutes'],
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
                'leave_type_id' => $normalizedAttendance['leave_type_id'],
                'status' => 'pending',
                'request_date' => $normalizedAttendance['request_date'],
                'from_date' => $normalizedAttendance['from_date'],
                'to_date' => $normalizedAttendance['to_date'],
                'from_time' => $normalizedAttendance['from_time'],
                'to_time' => $normalizedAttendance['to_time'],
                'leave_type' => $normalizedAttendance['leave_type'],
                'leave_days' => $normalizedAttendance['leave_days'],
                'leave_duration_type' => $normalizedAttendance['leave_duration_type'],
                'leave_hours' => $normalizedAttendance['leave_hours'],
                'requested_status' => $normalizedAttendance['requested_status'],
                'attachment_path' => $normalizedAttendance['attachment_path'],
                'reason' => $reason,
            ]);

            if ($requestType === 'leave' && $normalizedAttendance['leave_type_id'] && (float) $normalizedAttendance['leave_days'] > 0) {
                $leaveType = LeaveType::query()->find((int) $normalizedAttendance['leave_type_id']);
                if ($leaveType) {
                    $this->leaveService->reserveForRequest(
                        $user,
                        (int) $profile->id,
                        $leaveType,
                        (int) Carbon::parse((string) $normalizedAttendance['from_date'], self::TIMEZONE)->year,
                        (int) $request->id,
                        (float) $normalizedAttendance['leave_days']
                    );
                }
            }

            $approvalRequest->update([
                'target_type' => AttendanceRequest::class,
                'target_id' => $request->id,
            ]);

            $this->audit('attendance', 'submit_request', "Submit attendance request #{$request->id}", 'attendance_requests', $request->id);

            return $request;
        });
    }

    public function submitAttendanceAdjustmentRequest(User $user, array $payload): AttendanceAdjustment
    {
        return $this->handleTransaction(function () use ($user, $payload) {
            $profile = $user->employeeProfile;
            if (!$profile) {
                throw ValidationException::withMessages([
                    'attendance_record_id' => 'Ban chua co ho so nhan su de gui dieu chinh cong.',
                ]);
            }

            $record = AttendanceRecord::query()
                ->with(['workShift', 'employeeProfile'])
                ->whereKey((int) $payload['attendance_record_id'])
                ->where('employee_profile_id', $profile->id)
                ->first();

            if (!$record) {
                throw ValidationException::withMessages([
                    'attendance_record_id' => 'Khong tim thay ban ghi cong cua ban de dieu chinh.',
                ]);
            }

            try {
                $this->ensureMonthNotLocked($profile, Carbon::parse($record->work_date, self::TIMEZONE));
            } catch (\RuntimeException $exception) {
                throw ValidationException::withMessages([
                    'attendance_record_id' => $exception->getMessage(),
                ]);
            }

            if ((bool) $record->is_confirmed || ($record->approval_status ?? 'pending') === 'approved') {
                throw ValidationException::withMessages([
                    'attendance_record_id' => 'Ban ghi cong da duoc duyet, vui long lien he HR de dieu chinh.',
                ]);
            }

            $newCheckIn = filled($payload['new_check_in_at'] ?? null)
                ? Carbon::parse((string) $payload['new_check_in_at'], self::TIMEZONE)
                : null;
            $newCheckOut = filled($payload['new_check_out_at'] ?? null)
                ? Carbon::parse((string) $payload['new_check_out_at'], self::TIMEZONE)
                : null;

            if (!$newCheckIn && !$newCheckOut) {
                throw ValidationException::withMessages([
                    'new_check_in_at' => 'Can nhap it nhat check-in hoac check-out moi.',
                    'new_check_out_at' => 'Can nhap it nhat check-in hoac check-out moi.',
                ]);
            }

            $workDate = Carbon::parse($record->work_date, self::TIMEZONE)->toDateString();
            foreach ([$newCheckIn, $newCheckOut] as $dateTime) {
                if ($dateTime && $dateTime->toDateString() !== $workDate) {
                    throw ValidationException::withMessages([
                        'new_check_in_at' => 'Thoi gian de xuat phai nam trong dung ngay cong dang chon.',
                        'new_check_out_at' => 'Thoi gian de xuat phai nam trong dung ngay cong dang chon.',
                    ]);
                }
            }

            $oldCheckIn = $record->check_in_at ? Carbon::parse($record->check_in_at, self::TIMEZONE) : null;
            $oldCheckOut = $record->check_out_at ? Carbon::parse($record->check_out_at, self::TIMEZONE) : null;
            $resolvedCheckIn = $newCheckIn ?: $oldCheckIn;
            $resolvedCheckOut = $newCheckOut ?: $oldCheckOut;

            if ($resolvedCheckIn && $resolvedCheckOut && $resolvedCheckOut->lessThanOrEqualTo($resolvedCheckIn)) {
                throw ValidationException::withMessages([
                    'new_check_out_at' => 'Check-out de xuat phai sau check-in sau dieu chinh.',
                ]);
            }

            $checkInChanged = $newCheckIn && (!$oldCheckIn || !$newCheckIn->equalTo($oldCheckIn));
            $checkOutChanged = $newCheckOut && (!$oldCheckOut || !$newCheckOut->equalTo($oldCheckOut));

            if (!$checkInChanged && !$checkOutChanged) {
                throw ValidationException::withMessages([
                    'new_check_in_at' => 'Thoi gian de xuat khong khac du lieu hien tai.',
                    'new_check_out_at' => 'Thoi gian de xuat khong khac du lieu hien tai.',
                ]);
            }

            $pendingExists = AttendanceAdjustment::query()
                ->where('attendance_record_id', $record->id)
                ->where('status', 'pending')
                ->exists();

            if ($pendingExists) {
                throw ValidationException::withMessages([
                    'attendance_record_id' => 'Ban ghi nay da co yeu cau dieu chinh dang cho duyet.',
                ]);
            }

            [$previewAttendanceStatus, $previewDayStatus, $previewWorkedMinutes, $previewLateMinutes, $previewEarlyLeaveMinutes, $previewOvertimeMinutes] =
                $this->previewAttendanceMetrics($record, $resolvedCheckIn, $resolvedCheckOut);

            $approvalRequest = ApprovalRequest::query()->create([
                'request_type' => 'manual_adjustment',
                'target_type' => AttendanceAdjustment::class,
                'target_id' => 0,
                'requested_by' => $user->id,
                'status' => 'pending',
                'submitted_at' => now(self::TIMEZONE),
                'reason' => (string) $payload['reason'],
            ]);

            $adjustment = AttendanceAdjustment::query()->create([
                'attendance_record_id' => $record->id,
                'approval_request_id' => $approvalRequest->id,
                'old_check_in_at' => $record->check_in_at,
                'new_check_in_at' => $newCheckIn,
                'old_check_out_at' => $record->check_out_at,
                'new_check_out_at' => $newCheckOut,
                'reason' => (string) $payload['reason'],
                'status' => 'pending',
                'requested_by' => $user->id,
            ]);

            $approvalRequest->update([
                'target_id' => $adjustment->id,
            ]);

            $this->audit(
                'attendance',
                'submit_adjustment_request',
                "Submit attendance adjustment #{$adjustment->id}: {$previewAttendanceStatus}/{$previewDayStatus}, worked {$previewWorkedMinutes}, late {$previewLateMinutes}, early {$previewEarlyLeaveMinutes}, overtime {$previewOvertimeMinutes}",
                'attendance_adjustments',
                $adjustment->id
            );

            $this->notifyAttendanceAdjustmentApprovers($adjustment->fresh(['attendanceRecord.employeeProfile.user']), $user);

            return $adjustment->fresh(['attendanceRecord', 'approvalRequest']);
        });
    }

    public function reviewAttendanceAdjustmentRequest(AttendanceAdjustment $adjustment, User $reviewer, string $decision, ?string $note = null): AttendanceAdjustment
    {
        return $this->handleTransaction(function () use ($adjustment, $reviewer, $decision, $note) {
            if (!in_array($decision, ['approved', 'rejected'], true)) {
                throw new \RuntimeException('Quyet dinh duyet khong hop le.');
            }

            if ($adjustment->status !== 'pending') {
                throw new \RuntimeException('Yeu cau dieu chinh cong da duoc xu ly.');
            }

            $adjustment->loadMissing(['attendanceRecord.employeeProfile', 'approvalRequest']);
            $record = $adjustment->attendanceRecord;
            if (!$record) {
                throw new \RuntimeException('Khong tim thay ban ghi cong lien quan.');
            }

            $record->loadMissing(['employeeProfile.user', 'employeeProfile.position']);
            $this->ensureReviewerOutranksEmployee($reviewer, $record->employeeProfile);

            $this->ensureMonthNotLocked($record->employeeProfile, Carbon::parse($record->work_date, self::TIMEZONE));

            if ($decision === 'approved') {
                $nextCheckIn = $adjustment->new_check_in_at ? Carbon::parse($adjustment->new_check_in_at, self::TIMEZONE) : null;
                $nextCheckOut = $adjustment->new_check_out_at ? Carbon::parse($adjustment->new_check_out_at, self::TIMEZONE) : null;
                $snapshotCheckIn = $adjustment->old_check_in_at ? Carbon::parse($adjustment->old_check_in_at, self::TIMEZONE) : null;
                $snapshotCheckOut = $adjustment->old_check_out_at ? Carbon::parse($adjustment->old_check_out_at, self::TIMEZONE) : null;
                $resolvedCheckIn = $nextCheckIn ?: $snapshotCheckIn;
                $resolvedCheckOut = $nextCheckOut ?: $snapshotCheckOut;

                if ($resolvedCheckIn && $resolvedCheckOut && $resolvedCheckOut->lessThanOrEqualTo($resolvedCheckIn)) {
                    throw new \RuntimeException('Du lieu dieu chinh khong hop le.');
                }

                [$attendanceStatus, $dayStatus, $workedMinutes, $lateMinutes, $earlyLeaveMinutes, $overtimeMinutes] =
                    $this->previewAttendanceMetrics($record, $resolvedCheckIn, $resolvedCheckOut);

                $record->update([
                    'check_in_at' => $resolvedCheckIn,
                    'check_out_at' => $resolvedCheckOut,
                    'worked_minutes' => $workedMinutes,
                    'late_minutes' => $lateMinutes,
                    'early_leave_minutes' => $earlyLeaveMinutes,
                    'overtime_minutes' => $overtimeMinutes,
                    'missing_check_in' => !$resolvedCheckIn,
                    'missing_check_out' => !$resolvedCheckOut,
                    'attendance_status' => $attendanceStatus,
                    'day_status' => $dayStatus,
                    'approval_status' => 'approved',
                    'is_confirmed' => true,
                    'confirmed_by' => $reviewer->id,
                    'confirmed_at' => now(self::TIMEZONE),
                    'approval_note' => $note ?: 'Approved manual attendance adjustment',
                ]);

                AttendanceEvent::query()->create([
                    'attendance_record_id' => $record->id,
                    'employee_profile_id' => $record->employee_profile_id,
                    'event_type' => 'manual_adjustment',
                    'event_at' => now(self::TIMEZONE),
                    'source' => 'web',
                    'note' => 'Manual adjustment approved #' . $adjustment->id,
                    'created_by' => $reviewer->id,
                ]);

                $this->refreshMonthlySummary($record->fresh());
            }

            $adjustment->update([
                'status' => $decision,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(self::TIMEZONE),
                'review_note' => $note,
            ]);

            if ($adjustment->approvalRequest) {
                $adjustment->approvalRequest->update([
                    'status' => $decision,
                    'reviewed_by' => $reviewer->id,
                    'reviewed_at' => now(self::TIMEZONE),
                    'review_note' => $note,
                ]);
            }

            $this->audit(
                'attendance',
                'review_adjustment_' . $decision,
                "Review attendance adjustment #{$adjustment->id}",
                'attendance_adjustments',
                $adjustment->id
            );

            $this->notifyAttendanceAdjustmentRequester($adjustment->fresh(['attendanceRecord.employeeProfile.user']), $decision, $reviewer);

            return $adjustment->fresh(['attendanceRecord', 'approvalRequest.reviewer']);
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

        public function reviewApprovalRequest(ApprovalRequest $approvalRequest, User $reviewer, string $decision, ?string $note = null): ApprovalRequest
    {
        return $this->handleTransaction(function () use ($approvalRequest, $reviewer, $decision, $note) {
            if (!in_array($decision, ['approved', 'rejected'], true)) {
                throw new \RuntimeException('Trạng thái duyệt không hợp lệ.');
            }

            if ($approvalRequest->status !== 'pending') {
                throw new \RuntimeException('Yêu cầu này không còn ở trạng thái chờ duyệt.');
            }

            if ((int) $approvalRequest->requested_by === (int) $reviewer->id) {
                throw new \RuntimeException('Không được tự duyệt yêu cầu của chính mình.');
            }

            $note = trim((string) ($note ?? ''));
            if (mb_strlen($note) < 5) {
                throw new \RuntimeException('Vui lòng nhập ghi chú tối thiểu 5 ký tự.');
            }

            $approvalRequest->loadMissing('target');

            if (!in_array($approvalRequest->request_type, ['leave', 'late_early', 'forgot_check', 'business_trip', 'make_up', 'overtime'], true)) {
                throw new \RuntimeException('Loại yêu cầu không hợp lệ.');
            }

            if ($approvalRequest->request_type === 'leave') {
                if (!$reviewer->hasPositionCapability(PositionCapability::APPROVE_LEAVE) && !$reviewer->hasPositionCapability(PositionCapability::APPROVE_ATTENDANCE)) {
                    throw new \RuntimeException('Ban khong co quyen duyet nghi phep.');
                }
            } elseif (!$reviewer->hasPositionCapability(PositionCapability::APPROVE_ATTENDANCE)) {
                throw new \RuntimeException('Ban khong co quyen duyet cham cong.');
            }

            if (!$approvalRequest->target) {
                throw new \RuntimeException('Không tìm thấy đối tượng yêu cầu.');
            }

            $this->ensureReviewerOutranksEmployee(
                $reviewer,
                $this->resolveApprovalTargetEmployeeProfile($approvalRequest)
            );

            $approvalRequest->update([
                'status' => $decision,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(self::TIMEZONE),
                'review_note' => $note,
            ]);

            $target = $approvalRequest->target;

            if ($target instanceof AttendanceRequest) {
                if ($decision === 'approved') {
                    $this->validateAttendanceRequestForApproval($target);
                }

                $target->update([
                    'status' => $decision,
                    'applied_at' => $decision === 'approved' ? now(self::TIMEZONE) : null,
                    'applied_by' => $decision === 'approved' ? $reviewer->id : null,
                ]);

                if ($target->request_type === 'leave') {
                    $this->leaveService->finalizeRequest($reviewer, $target->fresh(['leaveType']), $decision);
                }

                if ($decision === 'approved') {
                    $this->applyAttendanceRequestToRecords($target, $reviewer, $note);
                }
            }

            if ($target instanceof OvertimeRequest) {
                $approvedMinutes = 0;

                if ($decision === 'approved') {
                    $this->validateOvertimeRequestForApproval($target);
                    $approvedMinutes = $this->calculateApprovedOvertimeMinutesFromRequest($target);

                    if ($approvedMinutes <= 0) {
                        throw new \RuntimeException('Khoảng thời gian tăng ca không hợp lệ với ca làm việc.');
                    }
                }

                $target->update([
                    'status' => $decision,
                    'approved_minutes' => $approvedMinutes,
                    'reviewed_by' => $reviewer->id,
                    'reviewed_at' => now(self::TIMEZONE),
                    'review_note' => $note,
                ]);

                if ($decision === 'approved') {
                    $this->applyOvertimeRequestToRecords($target, $reviewer, $note);
                }
            }

            $this->audit('attendance', 'review_request_' . $decision, "Review approval request #{$approvalRequest->id}", 'approval_requests', $approvalRequest->id);

            return $approvalRequest->fresh(['requester', 'reviewer', 'target']);
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
            $query
                ->where('approval_status', 'pending')
                ->where('is_confirmed', false)
                ->where(function (Builder $builder) {
                    $builder
                        ->where('late_minutes', '>', 0)
                        ->orWhere('early_leave_minutes', '>', 0)
                        ->orWhere('day_status', 'late')
                        ->orWhere('day_status', 'early_leave');
                });
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

        if ($actor && !AccessMatrix::canManageAllAttendance($actor)) {
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

    private function shouldAutoApproveAttendanceRecord(
        AttendanceRecord $record,
        int $lateMinutes,
        int $earlyLeaveMinutes,
        string $dayStatus
    ): bool {
        return false;
    }

    private function buildSummary(Collection $records): array
    {
        $normalizedStatuses = $records->map(fn (AttendanceRecord $record) => $this->normalizeAttendanceStatus($record->attendance_status));
        $resolvedMinutes = (int) $records->sum(fn (AttendanceRecord $record) => $this->resolveWorkedMinutes($record));
        $totalWorkUnits = (float) $records->sum(fn (AttendanceRecord $record) => $this->resolveWorkUnit($record));
        $approvedRecords = $records->filter(fn (AttendanceRecord $record) => ($record->approval_status ?? 'pending') === 'approved');
        $approvedBaseMinutes = (int) $approvedRecords->sum(fn (AttendanceRecord $record) => $this->resolveWorkedMinutes($record));
        $approvedWorkUnits = (float) $approvedRecords->sum(fn (AttendanceRecord $record) => $this->resolveWorkUnit($record));
        $approvedOvertimeMinutes = (int) $records
            ->filter(fn (AttendanceRecord $record) => ($record->approval_status ?? 'pending') === 'approved')
            ->sum(fn (AttendanceRecord $record) => (int) ($record->overtime_minutes ?? 0));
        $totalMinutes = $resolvedMinutes;
        $approvedMinutes = $approvedBaseMinutes;
        $confirmedCount = (int) $records->where('approval_status', 'approved')->count();
        $rejectedCount = (int) $records->where('approval_status', 'rejected')->count();
        $lateCount = (int) $normalizedStatuses->filter(fn (string $status) => $status === 'late')->count();
        $onTimeCount = (int) $normalizedStatuses->filter(fn (string $status) => $status === 'on_time')->count();
        $presentCount = (int) $normalizedStatuses->filter(fn (string $status) => in_array($status, ['on_time', 'late'], true))->count();
        $absentCount = (int) $normalizedStatuses->filter(fn (string $status) => $status === 'absent')->count();
        $earlyLeaveCount = (int) $records->filter(fn (AttendanceRecord $record) => ($record->day_status ?? null) === 'early_leave')->count();
        $missingCheckCount = (int) $records->filter(fn (AttendanceRecord $record) => in_array($this->normalizeDayStatus($record), ['missing_check_in', 'missing_check_out'], true))->count();
        $actionRequiredCount = (int) $records
            ->filter(fn (AttendanceRecord $record) => ($record->approval_status ?? 'pending') === 'pending')
            ->filter(fn (AttendanceRecord $record) => in_array($this->normalizeDayStatus($record), ['missing_check_in', 'missing_check_out', 'late', 'early_leave'], true))
            ->count();

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
            'missing_check_records' => $missingCheckCount,
            'action_required_records' => $actionRequiredCount,
            'total_worked_minutes' => $totalMinutes,
            'total_base_worked_minutes' => $resolvedMinutes,
            'total_overtime_minutes' => $approvedOvertimeMinutes,
            'total_worked_hours' => round($totalMinutes / 60, 2),
            'total_work_units' => round($totalWorkUnits, 2),
            'approved_worked_minutes' => $approvedMinutes,
            'approved_work_units' => round($approvedWorkUnits, 2),
        ];
    }

    private function buildMyRequestSummary(int $employeeProfileId): array
    {
        $attendancePending = AttendanceRequest::query()
            ->where('employee_profile_id', $employeeProfileId)
            ->where('status', 'pending')
            ->count();

        $overtimePending = OvertimeRequest::query()
            ->where('employee_profile_id', $employeeProfileId)
            ->where('status', 'pending')
            ->count();

        return [
            'pending_request_records' => (int) $attendancePending + (int) $overtimePending,
        ];
    }

    private function transformRecord(AttendanceRecord $record): array
    {
        $normalizedStatus = $this->normalizeAttendanceStatus($record->attendance_status);
        $resolvedWorkedMinutes = $this->resolveWorkedMinutes($record);
        $workDate = optional($record->work_date)->format('Y-m-d');
        $latestRequest = $workDate
            ? $this->findLatestAttendanceRequestForDate((int) $record->employee_profile_id, $workDate)
            : null;
        $hasRequest = (bool) $latestRequest;
        $hasApprovedRequest = $latestRequest?->status === 'approved';
        $requestPresenceLabel = $hasRequest
            ? sprintf('Co don (%s)', $this->approvalStatusLabel((string) $latestRequest->status))
            : 'Khong co don';
        $shiftConfig = $this->resolveShiftConfig($record);

        return [
            'id' => $record->id,
            'employee_profile_id' => $record->employee_profile_id,
            'employee_name' => $record->employeeProfile?->user?->name,
            'employee_code' => $record->employeeProfile?->employee_code,
            'employee_authority_level' => (int) ($record->employeeProfile?->position?->authority_level ?? 0),
            'department_name' => $record->employeeProfile?->department?->name,
            'position_name' => $record->employeeProfile?->position?->name,
            'work_date' => optional($record->work_date)->format('Y-m-d'),
            'check_in_at' => optional($record->check_in_at)->format('Y-m-d H:i:s'),
            'check_out_at' => optional($record->check_out_at)->format('Y-m-d H:i:s'),
            'shift_end_time' => substr((string) ($shiftConfig['end_time'] ?? ''), 0, 5) ?: null,
            'check_in_ip_address' => $record->check_in_ip_address,
            'check_out_ip_address' => $record->check_out_ip_address,
            'check_in_device' => $record->check_in_device,
            'check_out_device' => $record->check_out_device,
            'worked_minutes' => $resolvedWorkedMinutes,
            'work_unit' => $this->resolveWorkUnit($record),
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
            'rejected_at' => optional($record->rejected_at)->format('Y-m-d H:i:s'),
            'rejected_by_name' => $record->rejecter?->name,
            'reviewed_at' => optional($record->approval_status === 'rejected' ? $record->rejected_at : $record->confirmed_at)->format('Y-m-d H:i:s'),
            'reviewed_by_name' => $record->approval_status === 'rejected' ? $record->rejecter?->name : $record->confirmer?->name,
            'note' => $record->note,
            'formula_detail' => $this->extractFormulaFromNote($record->note),
            'approval_note' => $record->approval_note,
            'has_request' => $hasRequest,
            'has_approved_request' => $hasApprovedRequest,
            'request_presence' => $hasRequest ? 'has_request' : 'no_request',
            'request_presence_label' => $requestPresenceLabel,
            'request_type' => $latestRequest?->request_type,
            'request_status' => $latestRequest?->status,
            'status_label' => $this->attendanceResultLabel($record),
            'day_status_label' => $this->dayStatusLabel($record->day_status ?: $this->normalizeDayStatus($record)),
            'approval_status_label' => $this->approvalStatusLabel($record->approval_status ?: ($record->is_confirmed ? 'approved' : 'pending')),
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

    private function getLeaveBalancePayload(EmployeeProfile $profile, int $year): array
    {
        return EmployeeLeaveBalance::query()
            ->with('leaveType:id,name,code,is_paid,deducts_balance')
            ->where('employee_profile_id', $profile->id)
            ->where('year', $year)
            ->get()
            ->map(fn (EmployeeLeaveBalance $balance) => $this->leaveService->transformBalance($balance))
            ->values()
            ->all();
    }

    private function employeeOptionsForReviewer(?User $viewer): array
    {
        $query = EmployeeProfile::query()
            ->with(['user:id,name', 'department:id,name', 'position:id,authority_level'])
            ->orderBy('employee_code');

        $this->applyReviewerVisibilityToEmployeeQuery($query, $viewer);

        return $query
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
            'workShift:id,shift_name,start_time,end_time,break_start_time,break_end_time,standard_minutes,half_day_minutes,grace_minutes,late_grace_minutes,early_leave_grace_minutes,handover_break_minutes,allows_overtime,is_overnight',
            'workShift.overtimeRule:id,work_shift_id,start_time,end_time,hourly_rate',
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
            $shiftConfig = $this->resolveShiftConfig($record);
            $resolvedWorkedMinutes = $this->resolveWorkedMinutes($record);
            $workDate = Carbon::parse($record->work_date, self::TIMEZONE);
            $workShift = $record->workShift ?: $this->resolveWorkShift($record->employeeProfile, $workDate);
            $effectiveShift = $shiftConfig ?: $workShift;
            $normalizedStatus = $record->check_in_at
                ? ($this->determineLateMinutes(Carbon::parse($record->check_in_at, self::TIMEZONE), $effectiveShift) > 0 ? 'late' : 'on_time')
                : $this->normalizeAttendanceStatus($record->attendance_status);
            $dayStatus = $this->normalizeDayStatus($record);
            $lateMinutes = $record->check_in_at
                ? $this->determineLateMinutes(Carbon::parse($record->check_in_at, self::TIMEZONE), $effectiveShift)
                : 0;
            $earlyLeaveMinutes = $record->check_out_at
                ? $this->determineEarlyLeaveMinutes(Carbon::parse($record->check_out_at, self::TIMEZONE), $effectiveShift)
                : 0;
            $overtimeMinutes = $this->resolveApprovedOvertimeMinutesForDate(
                (int) $record->employee_profile_id,
                $workDate->toDateString()
            );

            if (
                $record->check_in_at
                && $record->check_out_at
                && !$record->missing_check_in
                && !$record->missing_check_out
            ) {
                [$normalizedStatus, $dayStatus] = $this->resolveStatusFromViolations($lateMinutes, $earlyLeaveMinutes);
            }

            $approvedRequest = $this->findApprovedAttendanceRequestForDate(
                (int) $record->employee_profile_id,
                $workDate->toDateString()
            );
            $approvedAdjustment = $this->findApprovedAttendanceAdjustmentForRecord((int) $record->id);

            if ($approvedRequest) {
                [$normalizedStatus, $dayStatus, $lateMinutes, $earlyLeaveMinutes, $resolvedWorkedMinutes] = $this->applyApprovedAttendanceRequestRule(
                    $approvedRequest,
                    $normalizedStatus,
                    $dayStatus,
                    $lateMinutes,
                    $earlyLeaveMinutes,
                    $resolvedWorkedMinutes,
                    $this->resolveStandardMinutesFromShiftConfig($shiftConfig)
                );
            }

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

            if ((int) ($record->overtime_minutes ?? 0) !== $overtimeMinutes) {
                $record->overtime_minutes = $overtimeMinutes;
                $dirty = true;
            }

            if ($workShift && (int) ($record->work_shift_id ?? 0) !== (int) $workShift->id) {
                $record->work_shift_id = $workShift->id;
                $dirty = true;
            }

            $expectedSnapshot = $this->buildShiftSnapshot($workShift);
            if ($expectedSnapshot && (is_array($record->shift_snapshot) ? $record->shift_snapshot : null) !== $expectedSnapshot) {
                $record->shift_snapshot = $expectedSnapshot;
                $dirty = true;
            }

            $requiresApprovedRequest = $this->requiresApprovedRequestForConfirmation($record, $lateMinutes, $earlyLeaveMinutes);
            if (
                $requiresApprovedRequest
                && !$approvedRequest
                && !$approvedAdjustment
                && (
                    ($record->approval_status ?? 'pending') === 'approved'
                    || (bool) $record->is_confirmed
                )
            ) {
                $record->approval_status = 'pending';
                $record->is_confirmed = false;
                $record->confirmed_by = null;
                $record->confirmed_at = null;
                $dirty = true;
            }

            if ($this->shouldAutoApproveAttendanceRecord($record, $lateMinutes, $earlyLeaveMinutes, $dayStatus)) {
                if (($record->approval_status ?? 'pending') !== 'approved') {
                    $record->approval_status = 'approved';
                    $dirty = true;
                }
                if (!(bool) $record->is_confirmed) {
                    $record->is_confirmed = true;
                    $dirty = true;
                }
            }

            if ((bool) $record->is_confirmed && ($record->approval_status ?? 'pending') !== 'approved') {
                if (!$requiresApprovedRequest || $approvedRequest) {
                    $record->approval_status = 'approved';
                } else {
                    $record->approval_status = 'pending';
                    $record->is_confirmed = false;
                    $record->confirmed_by = null;
                    $record->confirmed_at = null;
                }
                $dirty = true;
            }

            if (($record->approval_status ?? null) === 'rejected' && (bool) $record->is_confirmed) {
                $record->is_confirmed = false;
                $dirty = true;
            }

            if ($dirty) {
                $record->saveQuietly();
            }

            return $record;
        });
    }

    private function findApprovedAttendanceRequestForDate(int $employeeProfileId, string $workDate): ?AttendanceRequest
    {
        return $this->findLatestAttendanceRequestForDate($employeeProfileId, $workDate, ['approved']);
    }

    private function findApprovedAttendanceAdjustmentForRecord(int $attendanceRecordId): ?AttendanceAdjustment
    {
        return AttendanceAdjustment::query()
            ->where('attendance_record_id', $attendanceRecordId)
            ->where('status', 'approved')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->first();
    }

    private function findLatestAttendanceRequestForDate(int $employeeProfileId, string $workDate, ?array $statuses = null): ?AttendanceRequest
    {
        return AttendanceRequest::query()
            ->where('employee_profile_id', $employeeProfileId)
            ->when(
                is_array($statuses) && !empty($statuses),
                fn (Builder $builder) => $builder->whereIn('status', $statuses)
            )
            ->where(function (Builder $query) use ($workDate) {
                $query
                    ->whereDate('request_date', $workDate)
                    ->orWhere(function (Builder $rangeQuery) use ($workDate) {
                        $rangeQuery
                            ->whereNotNull('from_date')
                            ->whereNotNull('to_date')
                            ->whereDate('from_date', '<=', $workDate)
                            ->whereDate('to_date', '>=', $workDate);
                    });
            })
            ->orderByDesc('applied_at')
            ->orderByDesc('id')
            ->first();
    }

    private function applyApprovedAttendanceRequestRule(
        AttendanceRequest $request,
        string $attendanceStatus,
        string $dayStatus,
        int $lateMinutes,
        int $earlyLeaveMinutes,
        int $workedMinutes,
        ?int $standardMinutes = null
    ): array {
        switch ($request->request_type) {
            case 'leave':
                return [
                    'absent',
                    $request->leave_type === 'unpaid' ? 'unpaid_leave' : 'leave',
                    0,
                    0,
                    0,
                ];
            case 'business_trip':
                return [
                    'on_time',
                    'business_trip',
                    0,
                    0,
                    $standardMinutes ?: self::FULL_WORK_UNIT_MINUTES,
                ];
            case 'make_up':
                $makeUpMinutes = $this->calculateMakeUpMinutes($request);
                $updatedWorkedMinutes = $workedMinutes + $makeUpMinutes;
                $violationMinutes = $lateMinutes + $earlyLeaveMinutes;

                if ($makeUpMinutes <= 0 || $violationMinutes <= 0) {
                    return [
                        $attendanceStatus,
                        $dayStatus,
                        $lateMinutes,
                        $earlyLeaveMinutes,
                        $updatedWorkedMinutes,
                    ];
                }

                $remaining = max(0, $violationMinutes - $makeUpMinutes);
                $updatedLate = min($lateMinutes, $remaining);
                $updatedEarlyLeave = max(0, $remaining - $updatedLate);
                [$updatedAttendanceStatus, $updatedDayStatus] = $this->resolveStatusFromViolations($updatedLate, $updatedEarlyLeave);

                return [
                    $updatedAttendanceStatus,
                    $updatedDayStatus,
                    $updatedLate,
                    $updatedEarlyLeave,
                    $updatedWorkedMinutes,
                ];
            case 'forgot_check':
                $requestDate = optional($request->request_date)->format('Y-m-d');
                $resolvedCheckIn = null;
                $resolvedCheckOut = null;

                if ($requestDate && filled($request->from_time)) {
                    $resolvedCheckIn = Carbon::parse($requestDate . ' ' . $request->from_time, self::TIMEZONE);
                }

                if ($requestDate && filled($request->to_time)) {
                    $resolvedCheckOut = Carbon::parse($requestDate . ' ' . $request->to_time, self::TIMEZONE);
                    if ($resolvedCheckIn && $resolvedCheckOut->lessThanOrEqualTo($resolvedCheckIn)) {
                        $resolvedCheckOut->addDay();
                    }
                }

                if (!$resolvedCheckIn && !$resolvedCheckOut) {
                    return [
                        $attendanceStatus,
                        $dayStatus,
                        $lateMinutes,
                        $earlyLeaveMinutes,
                        $workedMinutes,
                    ];
                }

                return [
                    $attendanceStatus,
                    $dayStatus,
                    $lateMinutes,
                    $earlyLeaveMinutes,
                    $workedMinutes,
                ];
            case 'late_early':
                if ((string) $request->requested_status === 'early_leave') {
                    [$updatedAttendanceStatus, $updatedDayStatus] = $this->resolveStatusFromViolations($lateMinutes, 0);
                    return [
                        $updatedAttendanceStatus,
                        $updatedDayStatus,
                        $lateMinutes,
                        0,
                        $workedMinutes,
                    ];
                }

                [$updatedAttendanceStatus, $updatedDayStatus] = $this->resolveStatusFromViolations(0, $earlyLeaveMinutes);
                return [
                    $updatedAttendanceStatus,
                    $updatedDayStatus,
                    0,
                    $earlyLeaveMinutes,
                    $workedMinutes,
                ];
            default:
                return [
                    $attendanceStatus,
                    $dayStatus,
                    $lateMinutes,
                    $earlyLeaveMinutes,
                    $workedMinutes,
                ];
        }
    }

    private function resolveStatusFromViolations(int $lateMinutes, int $earlyLeaveMinutes): array
    {
        if ($earlyLeaveMinutes > 0) {
            return ['on_time', 'early_leave'];
        }

        if ($lateMinutes > 0) {
            return ['late', 'late'];
        }

        return ['on_time', 'present'];
    }

    private function attendanceMetricSnapshot(AttendanceRecord $record): array
    {
        return [
            'worked_minutes' => max(0, (int) $record->worked_minutes),
            'late_minutes' => max(0, (int) $record->late_minutes),
            'early_leave_minutes' => max(0, (int) $record->early_leave_minutes),
            'attendance_status' => (string) ($record->attendance_status ?? 'on_time'),
            'day_status' => (string) ($record->day_status ?? 'present'),
            'missing_check_in' => (bool) $record->missing_check_in,
            'missing_check_out' => (bool) $record->missing_check_out,
        ];
    }

    private function buildAttendanceFormulaDetail(AttendanceRequest $request, array $before, array $after): string
    {
        return match ($request->request_type) {
            'leave' => sprintf(
                'leave => worked=0, late=0, early=0, status=absent, day=%s',
                $request->leave_type === 'unpaid' ? 'unpaid_leave' : 'leave'
            ),
            'business_trip' => sprintf(
                'business_trip => worked:%d->%d, late=0, early=0, status=on_time, day=business_trip',
                $before['worked_minutes'],
                $after['worked_minutes']
            ),
            'forgot_check' => sprintf(
                'forgot_check => missing_check_in:%d->%d, missing_check_out:%d->%d',
                $before['missing_check_in'] ? 1 : 0,
                $after['missing_check_in'] ? 1 : 0,
                $before['missing_check_out'] ? 1 : 0,
                $after['missing_check_out'] ? 1 : 0
            ),
            'late_early' => sprintf(
                'late_early(%s) => late:%d->%d, early:%d->%d, status:%s->%s',
                (string) ($request->requested_status ?: 'late'),
                $before['late_minutes'],
                $after['late_minutes'],
                $before['early_leave_minutes'],
                $after['early_leave_minutes'],
                $before['attendance_status'],
                $after['attendance_status']
            ),
            'make_up' => sprintf(
                'make_up => worked:%d->%d, late:%d->%d, early:%d->%d',
                $before['worked_minutes'],
                $after['worked_minutes'],
                $before['late_minutes'],
                $after['late_minutes'],
                $before['early_leave_minutes'],
                $after['early_leave_minutes']
            ),
            default => sprintf(
                '%s => worked:%d->%d, late:%d->%d, early:%d->%d',
                (string) $request->request_type,
                $before['worked_minutes'],
                $after['worked_minutes'],
                $before['late_minutes'],
                $after['late_minutes'],
                $before['early_leave_minutes'],
                $after['early_leave_minutes']
            ),
        };
    }

    private function buildFormulaNote(string $formula): string
    {
        return 'Formula: ' . $formula;
    }

    private function extractFormulaFromNote(?string $note): ?string
    {
        if (!filled($note)) {
            return null;
        }

        $parts = array_reverse(array_map('trim', explode('|', (string) $note)));
        foreach ($parts as $part) {
            if (str_starts_with($part, 'Formula:')) {
                return trim(substr($part, strlen('Formula:')));
            }
        }

        return null;
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
        $shiftConfig = $this->resolveShiftConfig($record);

        if ($record->check_in_at && $record->check_out_at) {
            return $this->calculateWorkedMinutes(
                Carbon::parse($record->check_in_at, self::TIMEZONE),
                Carbon::parse($record->check_out_at, self::TIMEZONE),
                $shiftConfig
            );
        }

        return max(0, (int) ($record->worked_minutes ?? 0));
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

    private function resolveWorkUnit(AttendanceRecord $record): float
    {
        if (($record->day_status ?? null) === 'leave') {
            $workedMinutes = $this->resolveWorkedMinutes($record);

            return $workedMinutes > 0 ? $this->resolveWorkUnitByMinutes($workedMinutes) : 1.0;
        }

        if (($record->day_status ?? null) === 'unpaid_leave') {
            return 0.0;
        }

        if ($record->missing_check_in || $record->missing_check_out || ($record->check_in_at && !$record->check_out_at)) {
            return 0.0;
        }

        $workedMinutes = $this->resolveWorkedMinutes($record);

        if (
            $record->check_in_at
            && $record->check_out_at
            && max(0, (int) ($record->late_minutes ?? 0)) === 0
            && max(0, (int) ($record->early_leave_minutes ?? 0)) === 0
            && $this->normalizeAttendanceStatus($record->attendance_status) !== 'absent'
        ) {
            $shiftConfig = $this->resolveShiftConfig($record);
            $standardMinutes = (int) (($shiftConfig['standard_minutes'] ?? null) ?? self::FULL_WORK_UNIT_MINUTES);
            $workedMinutes = max($workedMinutes, $standardMinutes > 0 ? $standardMinutes : self::FULL_WORK_UNIT_MINUTES);
        }

        return $this->resolveWorkUnitByMinutes($workedMinutes);
    }

    private function resolveStandardMinutesForRecord(AttendanceRecord $record): int
    {
        return $this->resolveStandardMinutesFromShiftConfig($this->resolveShiftConfig($record));
    }

    private function resolveStandardMinutesFromShiftConfig(?array $shiftConfig): int
    {
        $standardMinutes = (int) (($shiftConfig['standard_minutes'] ?? null) ?? self::FULL_WORK_UNIT_MINUTES);

        return $standardMinutes > 0 ? $standardMinutes : self::FULL_WORK_UNIT_MINUTES;
    }

    private function resolveWorkUnitByMinutes(int $workedMinutes): float
    {
        if ($workedMinutes >= self::FULL_WORK_UNIT_MINUTES) {
            return 1.0;
        }

        if ($workedMinutes >= self::HALF_WORK_UNIT_MINUTES) {
            return 0.5;
        }

        return 0.0;
    }

    private function calculateWorkedMinutes(Carbon $checkInAt, Carbon $checkOutAt, ?array $shiftConfig = null): int
    {
        $minutes = max(0, (int) $checkInAt->diffInMinutes($checkOutAt, false));

        if ($minutes === 0 || !$shiftConfig) {
            return $minutes;
        }

        $breakMinutes = $this->calculateShiftBreakMinutes($checkInAt, $checkOutAt, $shiftConfig)
            + $this->calculateHandoverBreakMinutes($checkInAt, $checkOutAt, $shiftConfig);
        return max(0, $minutes - $breakMinutes);
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

        $totalPresentUnits = (float) $records->sum(fn (AttendanceRecord $item) => $this->resolveWorkUnit($item));
        $totalAbsentUnits = (float) $records->sum(
            fn (AttendanceRecord $item) => max(0.0, 1.0 - $this->resolveWorkUnit($item))
        );

        AttendanceMonthlySummary::query()->updateOrCreate(
            [
                'employee_profile_id' => $record->employee_profile_id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'total_working_days' => (int) $records->count(),
                'total_present_days' => round($totalPresentUnits, 2),
                'total_absent_days' => round($totalAbsentUnits, 2),
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
            ->where('status', 'active')
            ->with('employeeProfile.position')
            ->get(['id'])
            ->filter(fn (User $user) => $user->hasPositionCapability(PositionCapability::APPROVE_ATTENDANCE))
            ->pluck('id')
            ->values()
            ->all();

        if (empty($hrUserIds)) {
            return;
        }

        $employeeName = $record->employeeProfile?->user?->name ?: 'Nhan vien';
        $time = $eventType === 'check_in' ? $record->check_in_at : $record->check_out_at;
        $formattedTime = optional($time)->format('d/m/Y H:i');
        $actionLabel = $eventType === 'check_in' ? 'check-in' : 'check-out';

        try {
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
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function notifyAttendanceAdjustmentApprovers(AttendanceAdjustment $adjustment, User $requester): void
    {
        $record = $adjustment->attendanceRecord;

        if (!$record) {
            return;
        }

        $approverUserIds = User::query()
            ->where('status', 'active')
            ->with('employeeProfile.position')
            ->get(['id'])
            ->filter(fn (User $user) => $user->id !== $requester->id)
            ->filter(fn (User $user) => $user->hasPositionCapability(PositionCapability::APPROVE_ATTENDANCE))
            ->pluck('id')
            ->values()
            ->all();

        if (empty($approverUserIds)) {
            return;
        }

        $employeeName = $record->employeeProfile?->user?->name ?: $requester->name;
        $workDate = optional($record->work_date)->format('d/m/Y');

        try {
            $this->notificationService->createForUsers(
                $approverUserIds,
                'Yeu cau dieu chinh cong',
                "{$employeeName} vua gui yeu cau dieu chinh cong ngay {$workDate}.",
                [
                    'attendance_adjustment_id' => $adjustment->id,
                    'attendance_record_id' => $record->id,
                    'work_date' => optional($record->work_date)->format('Y-m-d'),
                    'request_type' => 'manual_adjustment',
                ],
                '/attendance/adjustments/approvals',
                null,
                'attendance',
                $requester->id,
                AttendanceAdjustment::class,
                $adjustment->id
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function notifyAttendanceAdjustmentRequester(AttendanceAdjustment $adjustment, string $decision, User $reviewer): void
    {
        $record = $adjustment->attendanceRecord;
        $requesterId = (int) ($adjustment->requested_by ?? 0);

        if (!$requesterId || !$record) {
            return;
        }

        $statusLabel = $decision === 'approved' ? 'duoc duyet' : 'bi tu choi';
        $workDate = optional($record->work_date)->format('d/m/Y');

        try {
            $this->notificationService->create(
                $requesterId,
                'Ket qua dieu chinh cong',
                "Yeu cau dieu chinh cong ngay {$workDate} da {$statusLabel}.",
                [
                    'attendance_adjustment_id' => $adjustment->id,
                    'attendance_record_id' => $record->id,
                    'work_date' => optional($record->work_date)->format('Y-m-d'),
                    'decision' => $decision,
                    'reviewer_id' => $reviewer->id,
                ],
                '/attendance/adjustments',
                null,
                'attendance',
                $reviewer->id,
                AttendanceAdjustment::class,
                $adjustment->id
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
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
                'label' => sprintf('Tháng %02d', $value),
            ])->all(),
            'years' => collect(range((int) now(self::TIMEZONE)->year - 3, (int) now(self::TIMEZONE)->year + 1))
                ->map(fn (int $value) => ['value' => $value, 'label' => (string) $value])
                ->all(),
        ];
    }

    private function getOvertimeDetails(User $actor, array $filters): array
    {
        $query = OvertimeRequest::query()
            ->with([
                'employeeProfile.user:id,name',
                'employeeProfile.department:id,name',
                'approvalRequest.reviewer:id,name',
            ])
            ->whereMonth('work_date', (int) $filters['month'])
            ->whereYear('work_date', (int) $filters['year'])
            ->latest('work_date')
            ->latest('id');

        if (!empty($filters['employee_profile_id'])) {
            $query->where('employee_profile_id', (int) $filters['employee_profile_id']);
        }

        if (!empty($filters['keyword'])) {
            $keyword = (string) $filters['keyword'];
            $query->where(function (Builder $builder) use ($keyword) {
                $builder
                    ->where('reason', 'like', "%{$keyword}%")
                    ->orWhere('status', 'like', "%{$keyword}%")
                    ->orWhereHas('employeeProfile.user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$keyword}%"))
                    ->orWhereHas('employeeProfile', fn (Builder $profileQuery) => $profileQuery->where('employee_code', 'like', "%{$keyword}%"));
            });
        }

        if (!AccessMatrix::canManageAllAttendance($actor)) {
            $query->where('employee_profile_id', $actor->employeeProfile?->id ?? 0);
        }

        return $query
            ->limit(200)
            ->get()
            ->map(fn (OvertimeRequest $request) => [
                'id' => $request->id,
                'employee_name' => $request->employeeProfile?->user?->name,
                'employee_code' => $request->employeeProfile?->employee_code,
                'department_name' => $request->employeeProfile?->department?->name,
                'work_date' => optional($request->work_date)->format('Y-m-d'),
                'start_at' => optional($request->start_at)->format('Y-m-d H:i:s'),
                'end_at' => optional($request->end_at)->format('Y-m-d H:i:s'),
                'requested_minutes' => (int) $request->requested_minutes,
                'approved_minutes' => (int) $request->approved_minutes,
                'status' => $request->status,
                'reason' => $request->reason,
                'review_note' => $request->review_note,
                'reviewed_by_name' => $request->approvalRequest?->reviewer?->name,
            ])
            ->values()
            ->all();
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

    private function getMyRequestHistory(User $user): array
    {
        $profileId = $user->employeeProfile?->id;

        if (!$profileId) {
            return [];
        }

        $attendanceRequests = AttendanceRequest::query()
            ->where('employee_profile_id', $profileId)
            ->with(['approvalRequest.reviewer:id,name', 'leaveType:id,name,requires_attachment'])
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (AttendanceRequest $request) => [
                'id' => 'attendance-' . $request->id,
                'target_type' => 'attendance',
                'request_type' => $request->request_type,
                'request_type_label' => $this->attendanceRequestTypeLabel($request->request_type),
                'request_date' => optional($request->request_date)->format('Y-m-d'),
                'period' => $this->formatRequestPeriod($request->from_date, $request->to_date, $request->from_time, $request->to_time),
                'from_date' => optional($request->from_date)->format('Y-m-d'),
                'to_date' => optional($request->to_date)->format('Y-m-d'),
                'from_time' => $request->from_time,
                'to_time' => $request->to_time,
                'leave_type' => $request->leave_type,
                'leave_type_name' => $request->leaveType?->name,
                'leave_type_requires_attachment' => (bool) $request->leaveType?->requires_attachment,
                'leave_days' => (float) ($request->leave_days ?? 0),
                'leave_duration_type' => $request->leave_duration_type,
                'leave_hours' => (float) ($request->leave_hours ?? 0),
                'requested_status' => $request->requested_status,
                'attachment_path' => $request->attachment_path,
                'attachment_url' => $request->attachment_path ? asset('storage/' . ltrim($request->attachment_path, '/')) : null,
                'status' => $request->status,
                'status_label' => $this->approvalStatusLabel($request->status),
                'reason' => $request->reason,
                'review_note' => $request->approvalRequest?->review_note,
                'reviewed_by_name' => $request->approvalRequest?->reviewer?->name,
                'reviewed_at' => optional($request->approvalRequest?->reviewed_at)->format('Y-m-d H:i:s'),
                'submitted_at' => optional($request->approvalRequest?->submitted_at ?? $request->created_at)->format('Y-m-d H:i:s'),
            ]);

        $overtimeRequests = OvertimeRequest::query()
            ->where('employee_profile_id', $profileId)
            ->with('approvalRequest.reviewer:id,name')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (OvertimeRequest $request) => [
                'id' => 'overtime-' . $request->id,
                'target_type' => 'overtime',
                'request_type' => 'overtime',
                'request_type_label' => $this->attendanceRequestTypeLabel('overtime'),
                'request_date' => optional($request->work_date)->format('Y-m-d'),
                'period' => trim(collect([
                    optional($request->start_at)->format('d/m/Y H:i'),
                    optional($request->end_at)->format('d/m/Y H:i'),
                ])->filter()->join(' - ')),
                'overtime_start_at' => optional($request->start_at)->format('Y-m-d H:i:s'),
                'overtime_end_at' => optional($request->end_at)->format('Y-m-d H:i:s'),
                'requested_minutes' => (int) ($request->requested_minutes ?? 0),
                'approved_minutes' => (int) ($request->approved_minutes ?? 0),
                'status' => $request->status,
                'status_label' => $this->approvalStatusLabel($request->status),
                'reason' => $request->reason,
                'review_note' => $request->review_note,
                'reviewed_by_name' => $request->approvalRequest?->reviewer?->name,
                'reviewed_at' => optional($request->approvalRequest?->reviewed_at ?? $request->reviewed_at)->format('Y-m-d H:i:s'),
                'submitted_at' => optional($request->approvalRequest?->submitted_at ?? $request->created_at)->format('Y-m-d H:i:s'),
            ]);

        return $attendanceRequests
            ->concat($overtimeRequests)
            ->sortByDesc('submitted_at')
            ->take(10)
            ->values()
            ->all();
    }

    private function getPendingApprovalRequests(array $filters = [], ?User $viewer = null): array
    {
        $query = ApprovalRequest::query()
            ->whereIn('request_type', ['leave', 'late_early', 'forgot_check', 'business_trip', 'make_up', 'overtime'])
            ->where('status', 'pending')
            ->with([
                'requester.employeeProfile:id,user_id,employee_code,department_id',
                'requester.employeeProfile.department:id,name',
                'reviewer:id,name',
                'target' => fn ($morphTo) => $morphTo->morphWith([
                    AttendanceRequest::class => ['leaveType:id,name,requires_attachment'],
                ]),
            ]);

        if ((bool) ($filters['only_leave'] ?? false)) {
            $query->where('request_type', 'leave');
        }

        $this->applyAttendanceApprovalRequestFilters($query, $filters, $viewer);

        return $query
            ->latest('id')
            ->get()
            ->map(fn (ApprovalRequest $approvalRequest) => $this->transformApprovalRequest($approvalRequest))
            ->values()
            ->all();
    }

    private function getReviewedApprovalRequests(array $filters = [], ?User $viewer = null): array
    {
        $query = ApprovalRequest::query()
            ->whereIn('request_type', ['leave', 'late_early', 'forgot_check', 'business_trip', 'make_up', 'overtime'])
            ->whereIn('status', ['approved', 'rejected'])
            ->with([
                'requester.employeeProfile:id,user_id,employee_code,department_id',
                'requester.employeeProfile.department:id,name',
                'reviewer:id,name',
                'target' => fn ($morphTo) => $morphTo->morphWith([
                    AttendanceRequest::class => ['leaveType:id,name,requires_attachment'],
                ]),
            ]);

        if ((bool) ($filters['only_leave'] ?? false)) {
            $query->where('request_type', 'leave');
        }

        $this->applyAttendanceApprovalRequestFilters($query, $filters, $viewer);

        return $query
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(fn (ApprovalRequest $approvalRequest) => $this->transformApprovalRequest($approvalRequest))
            ->values()
            ->all();
    }

    private function applyAttendanceApprovalRequestFilters(Builder $query, array $filters, ?User $viewer = null): void
    {
        $month = (int) ($filters['month'] ?? now(self::TIMEZONE)->month);
        $year = (int) ($filters['year'] ?? now(self::TIMEZONE)->year);
        $employeeProfileId = filled($filters['employee_profile_id'] ?? null) ? (int) $filters['employee_profile_id'] : null;
        $viewerProfileId = (int) ($viewer?->employeeProfile?->id ?? 0);
        $viewerLevel = (int) ($viewer?->employeeProfile?->position?->authority_level ?? 0);

        $query->whereHasMorph('target', [AttendanceRequest::class, OvertimeRequest::class], function (Builder $targetQuery, string $type) use ($month, $year, $employeeProfileId, $viewerProfileId, $viewerLevel) {
            if ($employeeProfileId) {
                $targetQuery->where('employee_profile_id', $employeeProfileId);
            }

            if ($viewerProfileId > 0) {
                $targetQuery->where('employee_profile_id', '!=', $viewerProfileId);
            }

            if ($viewerLevel > 0) {
                $targetQuery->whereHas('employeeProfile.position', fn (Builder $positionQuery) => $positionQuery->where('authority_level', '<', $viewerLevel));
            } else {
                $targetQuery->whereRaw('1 = 0');
            }

            if ($type === AttendanceRequest::class) {
                $targetQuery->where(function (Builder $dateQuery) use ($month, $year) {
                    $dateQuery
                        ->where(function (Builder $singleDateQuery) use ($month, $year) {
                            $singleDateQuery
                                ->whereNotNull('request_date')
                                ->whereMonth('request_date', $month)
                                ->whereYear('request_date', $year);
                        })
                        ->orWhere(function (Builder $rangeDateQuery) use ($month, $year) {
                            $rangeDateQuery
                                ->whereNotNull('from_date')
                                ->whereMonth('from_date', $month)
                                ->whereYear('from_date', $year);
                        });
                });

                return;
            }

            $targetQuery
                ->whereMonth('work_date', $month)
                ->whereYear('work_date', $year);
        });
    }

    private function transformApprovalRequest(ApprovalRequest $approvalRequest): array
    {
        $target = $approvalRequest->target;
        $requestDate = null;
        $period = '-';

        if ($target instanceof AttendanceRequest) {
            $requestDate = optional($target->request_date)->format('Y-m-d');
            $period = $this->formatRequestPeriod($target->from_date, $target->to_date, $target->from_time, $target->to_time);
        }

        if ($target instanceof OvertimeRequest) {
            $requestDate = optional($target->work_date)->format('Y-m-d');
            $period = trim(collect([
                optional($target->start_at)->format('d/m/Y H:i'),
                optional($target->end_at)->format('d/m/Y H:i'),
            ])->filter()->join(' - '));
        }

        return [
            'id' => $approvalRequest->id,
            'request_type' => $approvalRequest->request_type,
            'request_type_label' => $this->attendanceRequestTypeLabel($approvalRequest->request_type),
            'target_type' => $target instanceof OvertimeRequest ? 'overtime' : 'attendance',
            'employee_name' => $approvalRequest->requester?->name,
            'employee_code' => $approvalRequest->requester?->employeeProfile?->employee_code,
            'department_name' => $approvalRequest->requester?->employeeProfile?->department?->name,
            'request_date' => $requestDate,
            'period' => $period ?: '-',
            'reason' => $approvalRequest->reason,
            'leave_type' => $target instanceof AttendanceRequest ? $target->leave_type : null,
            'leave_type_id' => $target instanceof AttendanceRequest ? $target->leave_type_id : null,
            'leave_type_name' => $target instanceof AttendanceRequest ? $target->leaveType?->name : null,
            'leave_type_requires_attachment' => $target instanceof AttendanceRequest ? (bool) $target->leaveType?->requires_attachment : false,
            'leave_days' => $target instanceof AttendanceRequest ? (float) ($target->leave_days ?? 0) : null,
            'leave_duration_type' => $target instanceof AttendanceRequest ? $target->leave_duration_type : null,
            'leave_hours' => $target instanceof AttendanceRequest ? (float) ($target->leave_hours ?? 0) : null,
            'attachment_path' => $target instanceof AttendanceRequest ? $target->attachment_path : null,
            'attachment_url' => $target instanceof AttendanceRequest && $target->attachment_path ? asset('storage/' . ltrim($target->attachment_path, '/')) : null,
            'requested_status' => $target instanceof AttendanceRequest ? $target->requested_status : null,
            'from_date' => $target instanceof AttendanceRequest ? optional($target->from_date)->format('Y-m-d') : null,
            'to_date' => $target instanceof AttendanceRequest ? optional($target->to_date)->format('Y-m-d') : null,
            'from_time' => $target instanceof AttendanceRequest ? $target->from_time : null,
            'to_time' => $target instanceof AttendanceRequest ? $target->to_time : null,
            'overtime_start_at' => $target instanceof OvertimeRequest ? optional($target->start_at)->format('Y-m-d H:i:s') : null,
            'overtime_end_at' => $target instanceof OvertimeRequest ? optional($target->end_at)->format('Y-m-d H:i:s') : null,
            'requested_minutes' => $target instanceof OvertimeRequest ? (int) $target->requested_minutes : null,
            'approved_minutes' => $target instanceof OvertimeRequest ? (int) $target->approved_minutes : null,
            'status' => $approvalRequest->status,
            'status_label' => $this->approvalStatusLabel($approvalRequest->status),
            'submitted_at' => optional($approvalRequest->submitted_at)->format('Y-m-d H:i:s'),
            'reviewed_at' => optional($approvalRequest->reviewed_at)->format('Y-m-d H:i:s'),
            'reviewed_by_name' => $approvalRequest->reviewer?->name,
            'review_note' => $approvalRequest->review_note,
        ];
    }

    private function transformAdjustment(AttendanceAdjustment $item): array
    {
        $record = $item->attendanceRecord;

        return [
            'id' => $item->id,
            'attendance_record_id' => $item->attendance_record_id,
            'work_date' => optional($record?->work_date)->format('Y-m-d'),
            'employee_name' => $record?->employeeProfile?->user?->name,
            'employee_code' => $record?->employeeProfile?->employee_code,
            'department_name' => $record?->employeeProfile?->department?->name,
            'old_check_in_at' => optional($item->old_check_in_at)->format('Y-m-d H:i:s'),
            'new_check_in_at' => optional($item->new_check_in_at)->format('Y-m-d H:i:s'),
            'old_check_out_at' => optional($item->old_check_out_at)->format('Y-m-d H:i:s'),
            'new_check_out_at' => optional($item->new_check_out_at)->format('Y-m-d H:i:s'),
            'reason' => $item->reason,
            'status' => $item->status,
            'status_label' => $this->approvalStatusLabel($item->status),
            'review_note' => $item->review_note,
            'submitted_at' => optional($item->approvalRequest?->submitted_at ?? $item->created_at)->format('Y-m-d H:i:s'),
            'reviewed_at' => optional($item->reviewed_at)->format('Y-m-d H:i:s'),
            'reviewed_by_name' => $item->approvalRequest?->reviewer?->name,
        ];
    }

    private function attendanceRequestTypeLabel(?string $type): string
    {
        return match ($type) {
            'leave' => 'Xin nghỉ phép',
            'late_early' => 'Xin đi muộn / về sớm',
            'forgot_check' => 'Xin quên chấm công',
            'business_trip' => 'Xin công tác',
            'make_up' => 'Xin làm bù',
            'overtime' => 'Đăng ký tăng ca',
            default => 'Đơn chấm công',
        };
    }

    private function approvalStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'cancelled' => 'Đã hủy',
            default => '-',
        };
    }

    private function attendanceResultLabel(AttendanceRecord $record): string
    {
        if ($record->missing_check_in || $record->missing_check_out || ($record->check_in_at && !$record->check_out_at)) {
            return 'Chua tinh cong';
        }

        if (($record->approval_status ?? null) === 'rejected') {
            return 'Khong duyet cong';
        }

        $workUnit = $this->resolveWorkUnit($record);

        if ($workUnit >= 1.0) {
            return 'Du cong';
        }

        if ($workUnit >= 0.5) {
            return 'Nua cong';
        }

        return 'Khong cong';
    }

    private function dayStatusLabel(?string $status): string
    {
        return match ($status) {
            'present' => 'Di lam',
            'late' => 'Di muon',
            'early_leave' => 'Ve som',
            'leave' => 'Nghi phep',
            'unpaid_leave' => 'Nghi khong phep',
            'business_trip' => 'Cong tac',
            'missing_check_in' => 'Thieu check in',
            'missing_check_out' => 'Thieu check out',
            'absent' => 'Vang',
            default => 'Khong xac dinh',
        };
    }

    private function formatRequestPeriod($fromDate, $toDate, ?string $fromTime, ?string $toTime): string
    {
        $datePart = trim(collect([
            optional($fromDate)->format('d/m/Y'),
            optional($toDate)->format('d/m/Y'),
        ])->filter()->unique()->join(' - '));

        $timePart = trim(collect([$fromTime, $toTime])->filter()->join(' - '));

        return trim(collect([$datePart, $timePart])->filter()->join(' | '));
    }

    private function applyAttendanceRequestToRecords(AttendanceRequest $request, User $reviewer, ?string $note = null): void
    {
        $request->loadMissing(['employeeProfile.defaultWorkShift', 'employeeProfile.department']);
        $profile = $request->employeeProfile;

        if (!$profile) {
            return;
        }

        foreach ($this->requestDates($request) as $workDate) {
            $workDateAtTz = Carbon::parse($workDate, self::TIMEZONE);
            $this->ensureMonthNotLocked($profile, $workDateAtTz);
            $workShift = $this->resolveWorkShift($profile, $workDateAtTz);

            $record = AttendanceRecord::query()->firstOrNew([
                'employee_profile_id' => $profile->id,
                'work_date' => $workDate,
            ]);

            if (!$record->exists) {
                $record->fill([
                    'work_shift_id' => $workShift?->id,
                    'worked_minutes' => 0,
                    'late_minutes' => 0,
                    'early_leave_minutes' => 0,
                    'overtime_minutes' => 0,
                    'missing_check_in' => false,
                    'missing_check_out' => false,
                    'attendance_status' => 'on_time',
                    'approval_status' => 'pending',
                    'day_status' => 'present',
                    'is_confirmed' => false,
                    'shift_snapshot' => $this->buildShiftSnapshot($workShift),
                ]);
            }

            $beforeSnapshot = $this->attendanceMetricSnapshot($record);
            $this->applyAttendanceDayStatus($record, $request);
            $afterSnapshot = $this->attendanceMetricSnapshot($record);
            $formulaDetail = $this->buildAttendanceFormulaDetail($request, $beforeSnapshot, $afterSnapshot);
            $record->approval_status = 'approved';
            $record->is_confirmed = true;
            $record->confirmed_by = $reviewer->id;
            $record->confirmed_at = now(self::TIMEZONE);
            $record->rejected_by = null;
            $record->rejected_at = null;
            $record->approval_note = $note;
            $record->note = trim(collect(array_filter([
                $record->note,
                'Applied from request #' . $request->id,
                $this->buildFormulaNote($formulaDetail),
            ]))->implode(' | '));
            $record->save();

            $this->audit(
                'attendance',
                'apply_attendance_formula',
                "Apply {$request->request_type} request #{$request->id} to record #{$record->id}: {$formulaDetail}",
                'attendance_records',
                $record->id
            );
            $this->refreshMonthlySummary($record);
        }
    }

    private function applyOvertimeRequestToRecords(OvertimeRequest $request, User $reviewer, ?string $note = null): void
    {
        $request->loadMissing(['employeeProfile.defaultWorkShift', 'employeeProfile.department']);
        $profile = $request->employeeProfile;

        if (!$profile) {
            return;
        }

        $workDate = optional($request->work_date)->format('Y-m-d');

        if (!$workDate) {
            return;
        }

        $workDateAtTz = Carbon::parse($workDate, self::TIMEZONE);
        $this->ensureMonthNotLocked($profile, $workDateAtTz);
        $workShift = $this->resolveWorkShift($profile, $workDateAtTz);

        $record = AttendanceRecord::query()->firstOrNew([
            'employee_profile_id' => $profile->id,
            'work_date' => $workDate,
        ]);

        if (!$record->exists) {
            $record->fill([
                'work_shift_id' => $workShift?->id,
                'worked_minutes' => 0,
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
                'overtime_minutes' => 0,
                'missing_check_in' => true,
                'missing_check_out' => true,
                'attendance_status' => 'on_time',
                'approval_status' => 'pending',
                'day_status' => 'present',
                'is_confirmed' => false,
                'shift_snapshot' => $this->buildShiftSnapshot($workShift),
            ]);
        }

        $approvedMinutes = (int) ($request->approved_minutes ?? 0);
        if ($approvedMinutes <= 0) {
            return;
        }

        $beforeOvertime = max(0, (int) $record->overtime_minutes);
        $record->overtime_minutes = $this->resolveApprovedOvertimeMinutesForDate((int) $profile->id, $workDate);
        $record->approval_status = 'approved';
        $record->is_confirmed = true;
        $record->confirmed_by = $reviewer->id;
        $record->confirmed_at = now(self::TIMEZONE);
        $record->rejected_by = null;
        $record->rejected_at = null;
        $record->approval_note = $note;
        $record->note = trim(collect(array_filter([
            $record->note,
            'Applied overtime request #' . $request->id,
            $this->buildFormulaNote("overtime_minutes = approved_overtime_requests({$workDate}) = {$record->overtime_minutes}"),
        ]))->implode(' | '));
        $record->save();

        $this->audit(
            'attendance',
            'apply_overtime_formula',
            "Apply overtime request #{$request->id} to record #{$record->id}: overtime_minutes = approved requests total {$record->overtime_minutes} (before {$beforeOvertime}, request {$approvedMinutes})",
            'attendance_records',
            $record->id
        );
        $this->refreshMonthlySummary($record);
    }

    private function applyAttendanceDayStatus(AttendanceRecord $record, AttendanceRequest $request): void
    {
        switch ($request->request_type) {
            case 'leave':
                $isUnpaidLeave = $request->leave_type === 'unpaid';
                $standardMinutes = $this->resolveStandardMinutesForRecord($record);
                $leaveUnit = $this->leaveUnitForRecord($request, $record);
                $record->day_status = $isUnpaidLeave ? 'unpaid_leave' : 'leave';
                $record->attendance_status = 'absent';
                $record->worked_minutes = $isUnpaidLeave ? 0 : (int) round($standardMinutes * $leaveUnit);
                $record->late_minutes = 0;
                $record->early_leave_minutes = 0;
                $record->missing_check_in = false;
                $record->missing_check_out = false;
                break;
            case 'business_trip':
                $record->day_status = 'business_trip';
                $record->attendance_status = 'on_time';
                $record->worked_minutes = $this->resolveStandardMinutesForRecord($record);
                $record->late_minutes = 0;
                $record->early_leave_minutes = 0;
                $record->missing_check_in = false;
                $record->missing_check_out = false;
                break;
            case 'make_up':
                $makeUpMinutes = $this->calculateMakeUpMinutes($request);
                $record->worked_minutes = max(0, (int) $record->worked_minutes) + $makeUpMinutes;

                $lateMinutes = max(0, (int) $record->late_minutes);
                $earlyLeaveMinutes = max(0, (int) $record->early_leave_minutes);
                $violationMinutes = $lateMinutes + $earlyLeaveMinutes;
                if ($violationMinutes > 0 && $makeUpMinutes > 0) {
                    $remaining = max(0, $violationMinutes - $makeUpMinutes);
                    $record->late_minutes = min($lateMinutes, $remaining);
                    $record->early_leave_minutes = max(0, $remaining - $record->late_minutes);
                }

                [$attendanceStatus, $dayStatus] = $this->resolveStatusFromViolations(
                    (int) $record->late_minutes,
                    (int) $record->early_leave_minutes
                );
                $record->attendance_status = $attendanceStatus;
                $record->day_status = $dayStatus;
                break;
            case 'forgot_check':
                $requestDate = optional($request->request_date)->format('Y-m-d');
                $resolvedCheckIn = $record->check_in_at ? Carbon::parse($record->check_in_at, self::TIMEZONE) : null;
                $resolvedCheckOut = $record->check_out_at ? Carbon::parse($record->check_out_at, self::TIMEZONE) : null;

                if ($requestDate) {
                    $workDateAtTz = Carbon::parse($requestDate, self::TIMEZONE);
                    $workShift = $record->workShift ?: $this->resolveWorkShift($record->employeeProfile, $workDateAtTz);
                    [$shiftStart, $shiftEnd] = $this->shiftBoundaries($workDateAtTz, $workShift);

                    if (!$resolvedCheckIn) {
                        $resolvedCheckIn = filled($request->from_time)
                            ? Carbon::parse($requestDate . ' ' . $request->from_time, self::TIMEZONE)
                            : $shiftStart;
                    }

                    if (!$resolvedCheckOut) {
                        $resolvedCheckOut = filled($request->to_time)
                            ? Carbon::parse($requestDate . ' ' . $request->to_time, self::TIMEZONE)
                            : $shiftEnd;

                        if ($resolvedCheckIn && $resolvedCheckOut->lessThanOrEqualTo($resolvedCheckIn)) {
                            $resolvedCheckOut->addDay();
                        }
                    }
                }

                [$attendanceStatus, $dayStatus, $workedMinutes, $lateMinutes, $earlyLeaveMinutes, $overtimeMinutes] =
                    $this->previewAttendanceMetrics($record, $resolvedCheckIn, $resolvedCheckOut);

                $record->check_in_at = $resolvedCheckIn;
                $record->check_out_at = $resolvedCheckOut;
                $record->worked_minutes = $workedMinutes;
                $record->late_minutes = $lateMinutes;
                $record->early_leave_minutes = $earlyLeaveMinutes;
                $record->overtime_minutes = $overtimeMinutes;
                $record->attendance_status = $attendanceStatus;
                $record->day_status = $dayStatus;
                $record->missing_check_in = !$resolvedCheckIn;
                $record->missing_check_out = !$resolvedCheckOut;
                break;
            case 'late_early':
                $requestedStatus = (string) ($request->requested_status ?? '');

                if ($requestedStatus === 'early_leave') {
                    $record->early_leave_minutes = 0;
                } else {
                    $record->late_minutes = 0;
                }

                [$attendanceStatus, $dayStatus] = $this->resolveStatusFromViolations(
                    (int) $record->late_minutes,
                    (int) $record->early_leave_minutes
                );
                $record->attendance_status = $attendanceStatus;
                $record->day_status = $dayStatus;
                break;
            default:
                break;
        }
    }

    private function leaveUnitForRecord(AttendanceRequest $request, AttendanceRecord $record): float
    {
        $durationType = (string) ($request->leave_duration_type ?: 'full_day');

        if ($durationType === 'half_day') {
            return 0.5;
        }

        if ($durationType === 'hourly') {
            $standardMinutes = max(1, $this->resolveStandardMinutesForRecord($record));

            return min(1.0, max(0.0, ((float) $request->leave_hours * 60) / $standardMinutes));
        }

        return 1.0;
    }

    private function requestDates(AttendanceRequest $request): array
    {
        $fromDate = optional($request->from_date)->format('Y-m-d');
        $toDate = optional($request->to_date)->format('Y-m-d');
        $requestDate = optional($request->request_date)->format('Y-m-d');

        if ($fromDate && $toDate) {
            $start = Carbon::parse($fromDate, self::TIMEZONE)->startOfDay();
            $end = Carbon::parse($toDate, self::TIMEZONE)->startOfDay();

            if ($start->greaterThan($end)) {
                [$start, $end] = [$end, $start];
            }

            $dates = [];
            while ($start->lessThanOrEqualTo($end)) {
                $dates[] = $start->toDateString();
                $start->addDay();
            }

            return $dates;
        }

        if ($requestDate) {
            return [$requestDate];
        }

        if ($fromDate) {
            return [$fromDate];
        }

        if ($toDate) {
            return [$toDate];
        }

        return [];
    }
    private function validateAttendanceRequestOnSubmit(EmployeeProfile $profile, string $requestType, array $payload): array
    {
        if (!in_array($requestType, ['leave', 'late_early', 'forgot_check', 'business_trip', 'make_up'], true)) {
            throw ValidationException::withMessages([
                'request_type' => 'Loai don cham cong khong hop le.',
            ]);
        }

        $requestDate = filled($payload['request_date'] ?? null)
            ? Carbon::parse((string) $payload['request_date'], self::TIMEZONE)->toDateString()
            : null;
        $fromDate = filled($payload['from_date'] ?? null)
            ? Carbon::parse((string) $payload['from_date'], self::TIMEZONE)->toDateString()
            : null;
        $toDate = filled($payload['to_date'] ?? null)
            ? Carbon::parse((string) $payload['to_date'], self::TIMEZONE)->toDateString()
            : null;
        $fromTime = filled($payload['from_time'] ?? null) ? (string) $payload['from_time'] : null;
        $toTime = filled($payload['to_time'] ?? null) ? (string) $payload['to_time'] : null;
        $leaveDurationType = $requestType === 'leave' ? (string) ($payload['leave_duration_type'] ?? 'full_day') : null;
        $leaveHours = $requestType === 'leave' ? (float) ($payload['leave_hours'] ?? 0) : 0.0;
        $selectedLeaveType = null;
        if ($requestType === 'leave') {
            $selectedLeaveType = filled($payload['leave_type_id'] ?? null)
                ? LeaveType::query()->where('is_active', true)->find((int) $payload['leave_type_id'])
                : LeaveType::query()
                    ->where('is_active', true)
                    ->where('is_paid', ((string) ($payload['leave_type'] ?? 'paid')) !== 'unpaid')
                    ->orderByDesc('deducts_balance')
                    ->orderBy('id')
                    ->first();

            if (!$selectedLeaveType) {
                throw ValidationException::withMessages([
                    'leave_type_id' => 'Loai nghi phep khong hop le hoac da ngung dung.',
                ]);
            }
        }
        $leaveType = $selectedLeaveType ? ($selectedLeaveType->is_paid ? 'paid' : 'unpaid') : null;
        $requestedStatus = $requestType === 'late_early' ? (string) ($payload['requested_status'] ?? '') : null;

        if ($fromDate && $toDate && $fromDate > $toDate) {
            throw ValidationException::withMessages([
                'from_date' => 'Tu ngay phai truoc hoac bang den ngay.',
                'to_date' => 'Den ngay phai sau hoac bang tu ngay.',
            ]);
        }

        if ($requestType !== 'forgot_check' && (($fromTime && !$toTime) || (!$fromTime && $toTime))) {
            throw ValidationException::withMessages([
                'from_time' => 'Can nhap du gio bat dau va gio ket thuc.',
                'to_time' => 'Can nhap du gio bat dau va gio ket thuc.',
            ]);
        }

        if ($requestType !== 'forgot_check' && $fromTime && $toTime && $fromTime >= $toTime) {
            throw ValidationException::withMessages([
                'from_time' => 'Thoi gian bat dau phai truoc thoi gian ket thuc.',
                'to_time' => 'Thoi gian ket thuc phai sau thoi gian bat dau.',
            ]);
        }

        if (in_array($requestType, ['forgot_check', 'late_early'], true) && !$requestDate) {
            throw ValidationException::withMessages([
                'request_date' => 'Loai don nay yeu cau ngay ap dung.',
            ]);
        }

        if ($requestType === 'late_early' && !in_array($requestedStatus, ['late', 'early_leave'], true)) {
            throw ValidationException::withMessages([
                'requested_status' => 'Trang thai de nghi khong hop le.',
            ]);
        }

        if ($requestType === 'make_up') {
            $makeUpDate = $requestDate ?: $fromDate;
            if (!$makeUpDate) {
                throw ValidationException::withMessages([
                    'request_date' => 'Lam bu yeu cau ngay ap dung.',
                ]);
            }
            if (!$fromTime || !$toTime) {
                throw ValidationException::withMessages([
                    'from_time' => 'Lam bu yeu cau gio bat dau va gio ket thuc.',
                    'to_time' => 'Lam bu yeu cau gio bat dau va gio ket thuc.',
                ]);
            }
            $makeUpStartAt = Carbon::parse($makeUpDate . ' ' . $fromTime, self::TIMEZONE);
            $shiftEnd = $this->resolveShiftEndForProfile($profile, $makeUpStartAt);
            if ($makeUpStartAt->lessThanOrEqualTo($shiftEnd)) {
                throw ValidationException::withMessages([
                    'from_time' => 'Lam bu phai bat dau sau khi ket thuc gio hanh chinh.',
                ]);
            }
        }

        $affectedDates = $this->deriveSubmissionDates($requestDate, $fromDate, $toDate);
        if (empty($affectedDates)) {
            throw ValidationException::withMessages([
                'request_date' => 'Vui long nhap ngay ap dung hoac khoang ngay.',
                'from_date' => 'Vui long nhap ngay ap dung hoac khoang ngay.',
                'to_date' => 'Vui long nhap ngay ap dung hoac khoang ngay.',
            ]);
        }

        $today = now(self::TIMEZONE)->startOfDay();
        foreach ($affectedDates as $date) {
            $workDate = Carbon::parse($date, self::TIMEZONE)->startOfDay();
            if ($workDate->lt($today)) {
                throw ValidationException::withMessages([
                    'request_date' => 'Khong the gui don cho ngay trong qua khu.',
                    'from_date' => 'Khong the gui don cho ngay trong qua khu.',
                    'to_date' => 'Khong the gui don cho ngay trong qua khu.',
                ]);
            }
            try {
                $this->ensureMonthNotLocked($profile, $workDate);
            } catch (\RuntimeException) {
                throw ValidationException::withMessages([
                    'request_date' => 'Thang cong cua ngay da chon dang bi khoa.',
                    'from_date' => 'Thang cong cua ngay da chon dang bi khoa.',
                    'to_date' => 'Thang cong cua ngay da chon dang bi khoa.',
                ]);
            }
        }

        $leaveDays = 0.0;
        if ($requestType === 'leave') {
            if (!in_array($leaveDurationType, ['full_day', 'half_day', 'hourly'], true)) {
                throw ValidationException::withMessages([
                    'leave_duration_type' => 'Kieu thoi luong nghi khong hop le.',
                ]);
            }

            if (!$fromDate || !$toDate) {
                throw ValidationException::withMessages([
                    'from_date' => 'Nghi phep yeu cau tu ngay.',
                    'to_date' => 'Nghi phep yeu cau den ngay.',
                ]);
            }

            if (Carbon::parse($fromDate, self::TIMEZONE)->year !== Carbon::parse($toDate, self::TIMEZONE)->year) {
                throw ValidationException::withMessages([
                    'from_date' => 'Don nghi phep khong duoc vuot qua 2 nam. Vui long tach thanh 2 don.',
                    'to_date' => 'Don nghi phep khong duoc vuot qua 2 nam. Vui long tach thanh 2 don.',
                ]);
            }

            if (in_array($leaveDurationType, ['half_day', 'hourly'], true) && $fromDate !== $toDate) {
                throw ValidationException::withMessages([
                    'from_date' => 'Nghi nua ngay/theo gio chi ap dung cho mot ngay.',
                    'to_date' => 'Nghi nua ngay/theo gio chi ap dung cho mot ngay.',
                ]);
            }

            if ($leaveDurationType === 'hourly' && $leaveHours <= 0) {
                throw ValidationException::withMessages([
                    'leave_hours' => 'Vui long nhap so gio nghi.',
                ]);
            }

            $leaveDays = $this->calculateLeaveDays($profile, $affectedDates, $leaveDurationType, $leaveHours);
            if ($leaveDays <= 0) {
                throw ValidationException::withMessages([
                    'from_date' => 'Khoang nghi khong co ngay lam viec hop le de tinh phep.',
                    'to_date' => 'Khoang nghi khong co ngay lam viec hop le de tinh phep.',
                ]);
            }

            if ($selectedLeaveType?->max_days_per_request !== null && $leaveDays > (float) $selectedLeaveType->max_days_per_request) {
                throw ValidationException::withMessages([
                    'leave_type_id' => 'So ngay nghi vuot qua gioi han moi don cua loai phep.',
                ]);
            }

            if ($selectedLeaveType?->deducts_balance) {
                $balance = $this->leaveService->ensureBalance((int) $profile->id, (int) $selectedLeaveType->id, (int) Carbon::parse($fromDate, self::TIMEZONE)->year);
                if ($balance->available_days < $leaveDays) {
                    throw ValidationException::withMessages([
                        'leave_type_id' => 'So du phep hien con ' . round($balance->available_days, 2) . ' ngay, khong du cho don ' . round($leaveDays, 2) . ' ngay.',
                    ]);
                }
            }

            if ($selectedLeaveType?->requires_attachment && blank($payload['attachment_path'] ?? null)) {
                throw ValidationException::withMessages([
                    'attachment' => 'Loai nghi nay yeu cau tai len minh chung.',
                ]);
            }
        }

        $minDate = min($affectedDates);
        $maxDate = max($affectedDates);
        $duplicateQuery = AttendanceRequest::query()
            ->where('employee_profile_id', $profile->id)
            ->where('request_type', $requestType)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function (Builder $query) use ($minDate, $maxDate) {
                $query
                    ->whereBetween('request_date', [$minDate, $maxDate])
                    ->orWhere(function (Builder $rangeQuery) use ($minDate, $maxDate) {
                        $rangeQuery
                            ->whereNotNull('from_date')
                            ->whereNotNull('to_date')
                            ->whereDate('from_date', '<=', $maxDate)
                            ->whereDate('to_date', '>=', $minDate);
                    });
            });

        if ($requestType === 'late_early') {
            $duplicateQuery->where('requested_status', $requestedStatus);
        }

        if ($fromTime && $toTime) {
            $duplicateQuery->where('from_time', $fromTime)->where('to_time', $toTime);
        }

        if ($duplicateQuery->exists()) {
            throw ValidationException::withMessages([
                'request_type' => 'Da ton tai don cham cong cung loai trong khoang thoi gian nay.',
            ]);
        }

        return [
            'request_date' => $requestDate,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'from_time' => $fromTime,
            'to_time' => $toTime,
            'leave_type_id' => $selectedLeaveType?->id,
            'leave_type' => $leaveType,
            'leave_days' => $leaveDays,
            'leave_duration_type' => $leaveDurationType,
            'leave_hours' => $leaveHours,
            'requested_status' => $requestedStatus ?: null,
            'attachment_path' => filled($payload['attachment_path'] ?? null) ? (string) $payload['attachment_path'] : null,
        ];
    }
    private function validateOvertimeRequestOnSubmit(EmployeeProfile $profile, array $payload): array
    {
        if (filled($payload['start_at'] ?? null) && filled($payload['end_at'] ?? null)) {
            $startAt = Carbon::parse((string) $payload['start_at'], self::TIMEZONE);
            $endAt = Carbon::parse((string) $payload['end_at'], self::TIMEZONE);
        } else {
            if (!filled($payload['request_date'] ?? null)) {
                throw ValidationException::withMessages([
                    'request_date' => 'Vui long chon ngay tang ca.',
                ]);
            }

            $workDate = Carbon::parse((string) $payload['request_date'], self::TIMEZONE);
            $workShift = $this->resolveWorkShift($profile, $workDate->copy());
            $overtimeWindow = $this->resolveOvertimeWindowForShift($workShift, $workDate->copy());

            if (!$overtimeWindow) {
                throw ValidationException::withMessages([
                    'request_date' => 'Ca lam ngay nay chua cau hinh khung gio tang ca trong danh muc cham cong.',
                ]);
            }

            [$startAt, $endAt] = $overtimeWindow;
        }

        if ($endAt->lessThanOrEqualTo($startAt)) {
            throw ValidationException::withMessages([
                'request_date' => 'Khung gio tang ca trong danh muc khong hop le.',
                'start_at' => 'Bat dau tang ca phai truoc ket thuc tang ca.',
                'end_at' => 'Ket thuc tang ca phai sau bat dau tang ca.',
            ]);
        }

        $this->validateOvertimeRangeAgainstCatalog($profile, $startAt, $endAt);

        $today = now(self::TIMEZONE)->startOfDay();
        if ($startAt->copy()->startOfDay()->lt($today)) {
            throw ValidationException::withMessages([
                'request_date' => 'Khong the gui don tang ca cho ngay trong qua khu.',
                'start_at' => 'Khong the gui don tang ca cho ngay trong qua khu.',
                'end_at' => 'Khong the gui don tang ca cho ngay trong qua khu.',
            ]);
        }

        try {
            $this->ensureMonthNotLocked($profile, $startAt->copy());
            $this->ensureMonthNotLocked($profile, $endAt->copy());
        } catch (\RuntimeException) {
            throw ValidationException::withMessages([
                'request_date' => 'Thang cong cua thoi gian tang ca dang bi khoa.',
                'start_at' => 'Thang cong cua thoi gian tang ca dang bi khoa.',
                'end_at' => 'Thang cong cua thoi gian tang ca dang bi khoa.',
            ]);
        }

        $requestedMinutes = $this->calculateRequestedOvertimeMinutes($profile, $startAt, $endAt);
        if ($requestedMinutes <= 0) {
            throw ValidationException::withMessages([
                'request_date' => 'Khoang thoi gian tang ca khong hop le.',
                'start_at' => 'Khoang thoi gian tang ca khong hop le.',
                'end_at' => 'Khoang thoi gian tang ca khong hop le.',
            ]);
        }

        $hasDuplicate = OvertimeRequest::query()
            ->where('employee_profile_id', $profile->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists();

        if ($hasDuplicate) {
            throw ValidationException::withMessages([
                'request_date' => 'Da ton tai don tang ca trung thoi gian.',
                'start_at' => 'Da ton tai don tang ca trung thoi gian.',
                'end_at' => 'Da ton tai don tang ca trung thoi gian.',
            ]);
        }

        return [
            'start_at' => $startAt,
            'end_at' => $endAt,
            'requested_minutes' => $requestedMinutes,
        ];
    }

    private function deriveSubmissionDates(?string $requestDate, ?string $fromDate, ?string $toDate): array
    {
        if ($fromDate && $toDate) {
            $start = Carbon::parse($fromDate, self::TIMEZONE)->startOfDay();
            $end = Carbon::parse($toDate, self::TIMEZONE)->startOfDay();
            $dates = [];

            while ($start->lessThanOrEqualTo($end)) {
                $dates[] = $start->toDateString();
                $start->addDay();
            }

            return $dates;
        }

        if ($requestDate) {
            return [$requestDate];
        }

        if ($fromDate) {
            return [$fromDate];
        }

        if ($toDate) {
            return [$toDate];
        }

        return [];
    }

    private function calculateLeaveDays(EmployeeProfile $profile, array $dates, string $durationType = 'full_day', float $leaveHours = 0.0): float
    {
        $days = 0.0;

        foreach ($dates as $date) {
            $workDate = Carbon::parse($date, self::TIMEZONE);

            if ($this->isPaidHolidayDate($workDate)) {
                continue;
            }

            $workShift = $this->resolveWorkShift($profile, $workDate->copy());
            if (!$workShift && $workDate->isWeekend()) {
                continue;
            }

            if ($durationType === 'half_day') {
                $days += 0.5;
                continue;
            }

            if ($durationType === 'hourly') {
                $standardMinutes = $this->resolveStandardMinutesFromShiftConfig($this->buildShiftSnapshot($workShift));
                $standardHours = max(1.0, $standardMinutes / 60);
                $days += min(1.0, max(0.0, $leaveHours / $standardHours));
                continue;
            }

            $days += 1.0;
        }

        return round($days, 2);
    }

    private function isPaidHolidayDate(Carbon $workDate): bool
    {
        return Holiday::query()
            ->where('is_paid_leave', true)
            ->where(function (Builder $query) use ($workDate) {
                $query
                    ->whereDate('holiday_date', $workDate->toDateString())
                    ->orWhere(function (Builder $recurringQuery) use ($workDate) {
                        $recurringQuery
                            ->where('is_recurring', true)
                            ->whereMonth('holiday_date', $workDate->month)
                            ->whereDay('holiday_date', $workDate->day);
                    });
            })
            ->exists();
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
        $weekday = (int) $workDate->dayOfWeekIso;
        $assignment = EmployeeWorkShiftAssignment::query()
            ->with(['workShift.overtimeRule'])
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
            ->get()
            ->first(function (EmployeeWorkShiftAssignment $item) use ($weekday) {
                $weekdays = collect($item->weekdays ?? [])
                    ->map(fn ($value) => (int) $value)
                    ->filter(fn (int $value) => $value >= 1 && $value <= 7)
                    ->values();

                return $weekdays->isEmpty() || $weekdays->contains($weekday);
            });

        return $assignment?->workShift ?: $profile->defaultWorkShift;
    }

    private function buildOvertimeCatalogPayload(EmployeeProfile $profile, Carbon $workDate): ?array
    {
        $profile->loadMissing('defaultWorkShift.overtimeRule');
        $workShift = $this->resolveWorkShift($profile, $workDate->copy());

        if (!$workShift) {
            return null;
        }

        $window = $this->resolveOvertimeWindowForShift($workShift, $workDate->copy());
        $startAt = $window[0] ?? null;
        $endAt = $window[1] ?? null;
        $minutes = ($startAt && $endAt && $endAt->gt($startAt))
            ? $this->calculatePaidOvertimeMinutes($startAt, $endAt, $startAt, $endAt, $workShift)
            : 0;

        return [
            'work_date' => $workDate->toDateString(),
            'shift_name' => $workShift->shift_name,
            'allows_overtime' => (bool) $workShift->allows_overtime,
            'start_time' => $startAt?->format('H:i'),
            'end_time' => $endAt?->format('H:i'),
            'requested_minutes' => $minutes,
        ];
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
            'break_start_time' => $workShift->break_start_time,
            'break_end_time' => $workShift->break_end_time,
            'overtime_start_time' => $workShift->overtimeRule?->start_time,
            'overtime_end_time' => $workShift->overtimeRule?->end_time,
            'standard_minutes' => $workShift->standard_minutes,
            'half_day_minutes' => $workShift->half_day_minutes,
            'handover_break_minutes' => $workShift->handover_break_minutes,
            'overtime_hourly_rate' => $workShift->overtimeRule?->hourly_rate,
            'allows_overtime' => $workShift->allows_overtime,
            'grace_minutes' => $workShift->grace_minutes,
            'late_grace_minutes' => $workShift->late_grace_minutes,
            'early_leave_grace_minutes' => $workShift->early_leave_grace_minutes,
            'is_overnight' => $workShift->is_overnight,
        ];
    }

    private function determineLateMinutes(Carbon $checkInAt, WorkShift|array|null $workShift): int
    {
        $shiftConfig = $this->normalizeShiftConfig($workShift);
        [$expectedStart] = $this->shiftBoundaries($checkInAt, $shiftConfig);
        $allowedMinutes = (int) (($shiftConfig['late_grace_minutes'] ?? null) ?? ($shiftConfig['grace_minutes'] ?? self::DEFAULT_LATE_GRACE_MINUTES));
        if ($allowedMinutes <= 0) {
            $allowedMinutes = self::DEFAULT_LATE_GRACE_MINUTES;
        }
        $diffMinutes = (int) $expectedStart->diffInMinutes($checkInAt, false);

        return max(0, $diffMinutes - $allowedMinutes);
    }

    private function determineEarlyLeaveMinutes(Carbon $checkOutAt, WorkShift|array|null $workShift): int
    {
        $shiftConfig = $this->normalizeShiftConfig($workShift);
        [, $expectedEnd] = $this->shiftBoundaries($checkOutAt, $shiftConfig);
        $allowedMinutes = (int) (($shiftConfig['early_leave_grace_minutes'] ?? null) ?? ($shiftConfig['grace_minutes'] ?? self::DEFAULT_EARLY_LEAVE_GRACE_MINUTES));
        if ($allowedMinutes <= 0) {
            $allowedMinutes = self::DEFAULT_EARLY_LEAVE_GRACE_MINUTES;
        }
        $diffMinutes = (int) $expectedEnd->diffInMinutes($checkOutAt, false);

        return $diffMinutes < 0 ? max(0, abs($diffMinutes) - $allowedMinutes) : 0;
    }

    private function requiresApprovedRequestForConfirmation(AttendanceRecord $record, int $lateMinutes, int $earlyLeaveMinutes): bool
    {
        if (!$record->check_in_at || !$record->check_out_at) {
            return true;
        }

        return $lateMinutes > 0
            || $earlyLeaveMinutes > 0
            || in_array((string) ($record->day_status ?? ''), ['late', 'early_leave'], true);
    }

    private function determineOvertimeMinutes(int $workedMinutes, WorkShift|array|null $workShift, ?Carbon $checkInAt = null, ?Carbon $checkOutAt = null): int
    {
        $shiftConfig = $this->normalizeShiftConfig($workShift);

        if ($shiftConfig && !($shiftConfig['allows_overtime'] ?? true)) {
            return 0;
        }

        if (
            $shiftConfig
            && $checkInAt
            && $checkOutAt
            && filled($shiftConfig['overtime_start_time'] ?? null)
            && filled($shiftConfig['overtime_end_time'] ?? null)
        ) {
            $overtimeStartTime = $shiftConfig['overtime_start_time'];
            $overtimeEndTime = $shiftConfig['overtime_end_time'];

            return $this->calculateOverlapMinutes(
                $checkInAt,
                $checkOutAt,
                $this->resolveShiftTimePoint($checkInAt, (string) $overtimeStartTime),
                $this->resolveShiftTimePoint(
                    $checkInAt,
                    (string) $overtimeEndTime,
                    (bool) ($shiftConfig['is_overnight'] ?? false)
                        ? $this->minutesOfDay((string) $overtimeEndTime) <= $this->minutesOfDay((string) $overtimeStartTime)
                        : $this->minutesOfDay((string) $overtimeEndTime) <= $this->minutesOfDay((string) $overtimeStartTime)
                )
            );
        }

        $standardMinutes = (int) (($shiftConfig['standard_minutes'] ?? null) ?? self::FULL_WORK_UNIT_MINUTES);

        if ($standardMinutes <= 0) {
            $standardMinutes = self::FULL_WORK_UNIT_MINUTES;
        }

        return max(0, $workedMinutes - $standardMinutes);
    }
    private function validateAttendanceRequestForApproval(AttendanceRequest $request): void
    {
        $request->loadMissing('employeeProfile.department');

        if (!$request->employee_profile_id || !$request->employeeProfile) {
            throw new \RuntimeException('Khong tim thay nhan vien cho don cham cong.');
        }

        $workDates = $this->requestDates($request);

        if (empty($workDates)) {
            throw new \RuntimeException('Don cham cong chua co ngay ap dung hop le.');
        }

        foreach ($workDates as $workDate) {
            $this->ensureMonthNotLocked($request->employeeProfile, Carbon::parse($workDate, self::TIMEZONE));
        }

        if ($request->request_type === 'make_up') {
            $workDate = $workDates[0] ?? optional($request->request_date)->format('Y-m-d');
            $fromTime = filled($request->from_time) ? (string) $request->from_time : null;
            $toTime = filled($request->to_time) ? (string) $request->to_time : null;

            if (!$workDate || !$fromTime || !$toTime) {
                throw new \RuntimeException('Don lam bu thieu ngay hoac gio ap dung.');
            }

            $startAt = Carbon::parse($workDate . ' ' . $fromTime, self::TIMEZONE);
            $shiftEnd = $this->resolveShiftEndForProfile($request->employeeProfile, $startAt);
            if ($startAt->lessThanOrEqualTo($shiftEnd)) {
                throw new \RuntimeException('Lam bu phai bat dau sau khi ket thuc gio hanh chinh.');
            }
        }
    }
    private function validateOvertimeRequestForApproval(OvertimeRequest $request): void
    {
        $request->loadMissing('employeeProfile.department');

        if (!$request->employee_profile_id || !$request->employeeProfile) {
            throw new \RuntimeException('Khong tim thay nhan vien cho don tang ca.');
        }

        if (!$request->start_at || !$request->end_at) {
            throw new \RuntimeException('Don tang ca thieu moc thoi gian bat dau/ket thuc.');
        }

        if (Carbon::parse($request->end_at, self::TIMEZONE)->lessThanOrEqualTo(Carbon::parse($request->start_at, self::TIMEZONE))) {
            throw new \RuntimeException('Thoi gian ket thuc phai sau thoi gian bat dau.');
        }

        $startAt = Carbon::parse($request->start_at, self::TIMEZONE);
        $endAt = Carbon::parse($request->end_at, self::TIMEZONE);
        $this->ensureOvertimeRangeWithinCatalog($request->employeeProfile, $startAt, $endAt);

        $workDate = optional($request->work_date)->format('Y-m-d');
        if (!$workDate) {
            throw new \RuntimeException('Don tang ca thieu ngay ap dung.');
        }

        $this->ensureMonthNotLocked($request->employeeProfile, Carbon::parse($workDate, self::TIMEZONE));
    }

    private function calculateApprovedOvertimeMinutesFromRequest(OvertimeRequest $request): int
    {
        $request->loadMissing('employeeProfile.defaultWorkShift');
        $profile = $request->employeeProfile;

        if (!$profile || !$request->start_at || !$request->end_at) {
            return 0;
        }

        $startAt = Carbon::parse($request->start_at, self::TIMEZONE);
        $endAt = Carbon::parse($request->end_at, self::TIMEZONE);

        if ($endAt->lessThanOrEqualTo($startAt)) {
            return 0;
        }

        return $this->calculateRequestedOvertimeMinutes($profile, $startAt, $endAt);
    }

    private function validateOvertimeRangeAgainstCatalog(EmployeeProfile $profile, Carbon $startAt, Carbon $endAt): void
    {
        try {
            $this->ensureOvertimeRangeWithinCatalog($profile, $startAt, $endAt);
        } catch (\RuntimeException $exception) {
            throw ValidationException::withMessages([
                'start_at' => $exception->getMessage(),
                'end_at' => $exception->getMessage(),
            ]);
        }
    }

    private function ensureOvertimeRangeWithinCatalog(EmployeeProfile $profile, Carbon $startAt, Carbon $endAt): void
    {
        $workShift = $this->resolveWorkShift($profile, $startAt->copy());

        if ($workShift && !($workShift->allows_overtime ?? true)) {
            throw new \RuntimeException('Ca lam hien tai khong cho phep dang ky tang ca.');
        }

        $overtimeWindow = $this->resolveOvertimeWindowForShift($workShift, $startAt->copy());

        if ($overtimeWindow) {
            [$overtimeStart, $overtimeEnd] = $overtimeWindow;

            if ($startAt->lt($overtimeStart) || $endAt->gt($overtimeEnd)) {
                throw new \RuntimeException(sprintf(
                    'Thoi gian tang ca phai nam trong khung %s - %s cua danh muc cham cong.',
                    $overtimeStart->format('H:i'),
                    $overtimeEnd->format('H:i')
                ));
            }

            return;
        }

        $shiftEnd = $this->resolveShiftEndForProfile($profile, $startAt);
        if ($startAt->lessThanOrEqualTo($shiftEnd)) {
            throw new \RuntimeException('Tang ca phai bat dau sau khi ket thuc gio hanh chinh.');
        }
    }

    private function calculateMakeUpMinutes(AttendanceRequest $request): int
    {
        if ($request->request_type !== 'make_up') {
            return 0;
        }

        $fromTime = filled($request->from_time) ? (string) $request->from_time : null;
        $toTime = filled($request->to_time) ? (string) $request->to_time : null;
        if (!$fromTime || !$toTime || $fromTime >= $toTime) {
            return 0;
        }

        $date = optional($request->request_date)->format('Y-m-d')
            ?: optional($request->from_date)->format('Y-m-d')
            ?: optional($request->to_date)->format('Y-m-d');

        if (!$date) {
            return 0;
        }

        $startAt = Carbon::parse($date . ' ' . $fromTime, self::TIMEZONE);
        $endAt = Carbon::parse($date . ' ' . $toTime, self::TIMEZONE);
        if ($endAt->lessThanOrEqualTo($startAt)) {
            return 0;
        }

        return $this->calculateWorkedMinutes($startAt, $endAt);
    }

    private function calculateRequestedOvertimeMinutes(EmployeeProfile $profile, Carbon $startAt, Carbon $endAt): int
    {
        $workShift = $this->resolveWorkShift($profile, $startAt->copy());

        if (!$workShift) {
            return $this->calculateWorkedMinutes($startAt, $endAt);
        }

        $overtimeStartTime = $this->resolveOvertimeRuleStartTime($workShift);
        $overtimeEndTime = $this->resolveOvertimeRuleEndTime($workShift);

        if (filled($overtimeStartTime) && filled($overtimeEndTime)) {
            $overtimeStart = $this->resolveShiftTimePoint($startAt, (string) $overtimeStartTime);
            $overtimeEnd = $this->resolveShiftTimePoint(
                $startAt,
                (string) $overtimeEndTime,
                $this->minutesOfDay((string) $overtimeEndTime) <= $this->minutesOfDay((string) $overtimeStartTime)
            );

            return $this->calculatePaidOvertimeMinutes($startAt, $endAt, $overtimeStart, $overtimeEnd, $workShift);
        }

        [$shiftStart, $shiftEnd] = $this->shiftBoundaries($startAt->copy(), $workShift);
        $minutes = 0;

        if ($startAt->lt($shiftStart)) {
            $beforeEnd = $endAt->lt($shiftStart) ? $endAt : $shiftStart;
            $minutes += $this->calculateWorkedMinutes($startAt, $beforeEnd);
        }

        if ($endAt->gt($shiftEnd)) {
            $afterStart = $startAt->gt($shiftEnd) ? $startAt : $shiftEnd;
            $minutes += $this->calculatePaidOvertimeMinutes($afterStart, $endAt, $shiftEnd, $endAt, $workShift);
        }

        return max(0, $minutes);
    }

    private function previewAttendanceMetrics(
        AttendanceRecord $record,
        ?Carbon $resolvedCheckIn,
        ?Carbon $resolvedCheckOut
    ): array {
        $record->loadMissing(['employeeProfile', 'workShift']);
        $workDate = Carbon::parse($record->work_date, self::TIMEZONE);
        $workShift = $record->workShift ?: $this->resolveWorkShift($record->employeeProfile, $workDate);

        if (!$resolvedCheckIn || !$resolvedCheckOut) {
            return [
                $resolvedCheckIn ? 'on_time' : 'absent',
                $resolvedCheckIn ? 'missing_check_out' : 'missing_check_in',
                0,
                $resolvedCheckIn ? $this->determineLateMinutes($resolvedCheckIn, $workShift) : 0,
                0,
                0,
            ];
        }

        $workedMinutes = $this->calculateWorkedMinutes($resolvedCheckIn, $resolvedCheckOut, $this->shiftConfigFromWorkShift($workShift));
        $lateMinutes = $this->determineLateMinutes($resolvedCheckIn, $workShift);
        $earlyLeaveMinutes = $this->determineEarlyLeaveMinutes($resolvedCheckOut, $workShift);
        $overtimeMinutes = $this->resolveApprovedOvertimeMinutesForDate(
            (int) $record->employee_profile_id,
            Carbon::parse($record->work_date, self::TIMEZONE)->toDateString()
        );
        $dayStatus = $this->determineDayStatus($resolvedCheckIn, $resolvedCheckOut, $workShift);

        return [
            $lateMinutes > 0 ? 'late' : 'on_time',
            $dayStatus,
            $workedMinutes,
            $lateMinutes,
            $earlyLeaveMinutes,
            $overtimeMinutes,
        ];
    }

    private function resolveApprovedOvertimeMinutesForDate(int $employeeProfileId, string $workDate): int
    {
        return (int) OvertimeRequest::query()
            ->where('employee_profile_id', $employeeProfileId)
            ->whereDate('work_date', $workDate)
            ->where('status', 'approved')
            ->with('employeeProfile.defaultWorkShift')
            ->get()
            ->sum(function (OvertimeRequest $request) {
                $approvedMinutes = max(0, (int) ($request->approved_minutes ?? 0));
                if ($approvedMinutes === 0) {
                    return 0;
                }

                return min($approvedMinutes, $this->calculateApprovedOvertimeMinutesFromRequest($request));
            });
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

    private function shiftBoundaries(Carbon $dateTime, WorkShift|array|null $workShift): array
    {
        $shiftConfig = $this->normalizeShiftConfig($workShift);
        $startTime = $shiftConfig['start_time'] ?? sprintf('%02d:%02d:00', self::WORK_START_HOUR, self::WORK_START_MINUTE);
        $endTime = $shiftConfig['end_time'] ?? sprintf('%02d:%02d:00', self::WORK_END_HOUR, self::WORK_END_MINUTE);
        [$startHour, $startMinute] = array_map('intval', explode(':', substr((string) $startTime, 0, 5)));
        [$endHour, $endMinute] = array_map('intval', explode(':', substr((string) $endTime, 0, 5)));
        $start = $dateTime->copy()->startOfDay()->setTime($startHour, $startMinute);
        $end = $dateTime->copy()->startOfDay()->setTime($endHour, $endMinute);

        if (
            (bool) ($shiftConfig['is_overnight'] ?? false)
            && $this->minutesOfDay((string) $endTime) <= $this->minutesOfDay((string) $startTime)
        ) {
            $end->addDay();
        }

        return [
            $start,
            $end,
        ];
    }

    private function resolveShiftTimePoint(Carbon $baseDate, string $time, bool $nextDay = false): Carbon
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));
        $point = $baseDate->copy()->startOfDay()->setTime($hour, $minute);

        return $nextDay ? $point->addDay() : $point;
    }

    private function resolveTimeForRecordDate(AttendanceRecord $record, string $time): Carbon
    {
        $baseDate = Carbon::parse($record->work_date, self::TIMEZONE);
        $resolved = $this->resolveShiftTimePoint($baseDate, $time);
        $checkInAt = $record->check_in_at ? Carbon::parse($record->check_in_at, self::TIMEZONE) : null;

        if ($checkInAt && $resolved->lessThanOrEqualTo($checkInAt)) {
            $resolved->addDay();
        }

        return $resolved;
    }

    private function calculateOverlapMinutes(Carbon $rangeStart, Carbon $rangeEnd, Carbon $windowStart, Carbon $windowEnd): int
    {
        $start = $rangeStart->greaterThan($windowStart) ? $rangeStart : $windowStart;
        $end = $rangeEnd->lessThan($windowEnd) ? $rangeEnd : $windowEnd;

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return $this->calculateWorkedMinutes($start, $end);
    }

    private function calculatePaidOvertimeMinutes(
        Carbon $rangeStart,
        Carbon $rangeEnd,
        Carbon $windowStart,
        Carbon $windowEnd,
        ?WorkShift $workShift
    ): int {
        $minutes = $this->calculateOverlapMinutes($rangeStart, $rangeEnd, $windowStart, $windowEnd);
        $handoverMinutes = max(0, (int) ($workShift?->handover_break_minutes ?? 0));

        if ($minutes === 0 || $handoverMinutes === 0) {
            return $minutes;
        }

        $handoverEnd = $windowStart->copy()->addMinutes($handoverMinutes);
        $unpaidMinutes = $this->calculateOverlapMinutes($rangeStart, $rangeEnd, $windowStart, $handoverEnd);

        return max(0, $minutes - $unpaidMinutes);
    }

    private function resolveShiftConfig(AttendanceRecord $record): ?array
    {
        $snapshot = is_array($record->shift_snapshot) ? $record->shift_snapshot : [];
        $workDate = $record->work_date ? Carbon::parse($record->work_date, self::TIMEZONE) : null;
        $resolvedWorkShift = ($record->relationLoaded('workShift') && $record->workShift)
            ? $record->workShift
            : (($record->employeeProfile && $workDate) ? $this->resolveWorkShift($record->employeeProfile, $workDate) : null);
        $liveConfig = $this->shiftConfigFromWorkShift($resolvedWorkShift) ?? [];

        $config = array_merge($liveConfig, $snapshot);

        return $config !== [] ? $config : null;
    }

    private function shiftConfigFromWorkShift(?WorkShift $workShift): ?array
    {
        if (!$workShift) {
            return null;
        }

        return [
            'start_time' => $workShift->start_time,
            'end_time' => $workShift->end_time,
            'break_start_time' => $workShift->break_start_time,
            'break_end_time' => $workShift->break_end_time,
            'overtime_start_time' => $workShift->overtimeRule?->start_time,
            'overtime_end_time' => $workShift->overtimeRule?->end_time,
            'standard_minutes' => $workShift->standard_minutes,
            'handover_break_minutes' => $workShift->handover_break_minutes,
            'allows_overtime' => $workShift->allows_overtime,
            'grace_minutes' => $workShift->grace_minutes,
            'late_grace_minutes' => $workShift->late_grace_minutes,
            'early_leave_grace_minutes' => $workShift->early_leave_grace_minutes,
            'is_overnight' => $workShift->is_overnight,
        ];
    }

    private function normalizeShiftConfig(WorkShift|array|null $workShift): array
    {
        if ($workShift instanceof WorkShift) {
            return $this->shiftConfigFromWorkShift($workShift) ?? [];
        }

        return is_array($workShift) ? $workShift : [];
    }

    private function calculateShiftBreakMinutes(Carbon $checkInAt, Carbon $checkOutAt, array $shiftConfig): int
    {
        $breakStart = $shiftConfig['break_start_time'] ?? null;
        $breakEnd = $shiftConfig['break_end_time'] ?? null;

        if (!filled($breakStart) || !filled($breakEnd)) {
            return 0;
        }

        $breakStartAt = $this->resolveShiftTimePoint($checkInAt, (string) $breakStart);
        $breakEndAt = $this->resolveShiftTimePoint(
            $checkInAt,
            (string) $breakEnd,
            (bool) ($shiftConfig['is_overnight'] ?? false) && $this->minutesOfDay((string) $breakEnd) <= $this->minutesOfDay((string) $breakStart)
        );

        return $this->calculateOverlapMinutes($checkInAt, $checkOutAt, $breakStartAt, $breakEndAt);
    }

    private function calculateHandoverBreakMinutes(Carbon $checkInAt, Carbon $checkOutAt, array $shiftConfig): int
    {
        $handoverMinutes = max(0, (int) ($shiftConfig['handover_break_minutes'] ?? 0));
        $endTime = $shiftConfig['end_time'] ?? null;

        if ($handoverMinutes === 0 || !filled($endTime)) {
            return 0;
        }

        $shiftEndAt = $this->resolveShiftTimePoint(
            $checkInAt,
            (string) $endTime,
            (bool) ($shiftConfig['is_overnight'] ?? false)
                && $this->minutesOfDay((string) $endTime) <= $this->minutesOfDay((string) ($shiftConfig['start_time'] ?? $endTime))
        );
        $handoverStartAt = $shiftEndAt->copy()->subMinutes($handoverMinutes);

        return $this->calculateOverlapMinutes($checkInAt, $checkOutAt, $handoverStartAt, $shiftEndAt);
    }

    private function resolveOvertimeStartForShift(?WorkShift $workShift, Carbon $dateTime): ?Carbon
    {
        $overtimeStartTime = $this->resolveOvertimeRuleStartTime($workShift);

        if (!$workShift || !filled($overtimeStartTime)) {
            return null;
        }

        return $this->resolveShiftTimePoint($dateTime, (string) $overtimeStartTime);
    }

    private function resolveOvertimeWindowForShift(?WorkShift $workShift, Carbon $dateTime): ?array
    {
        $overtimeStartTime = $this->resolveOvertimeRuleStartTime($workShift);
        $overtimeEndTime = $this->resolveOvertimeRuleEndTime($workShift);

        if (!$workShift || !filled($overtimeStartTime) || !filled($overtimeEndTime)) {
            return null;
        }

        $overtimeStart = $this->resolveShiftTimePoint($dateTime, (string) $overtimeStartTime);
        $overtimeEnd = $this->resolveShiftTimePoint(
            $dateTime,
            (string) $overtimeEndTime,
            $this->minutesOfDay((string) $overtimeEndTime) <= $this->minutesOfDay((string) $overtimeStartTime)
        );

        return [$overtimeStart, $overtimeEnd];
    }

    private function resolveOvertimeRuleStartTime(?WorkShift $workShift): ?string
    {
        return $workShift?->overtimeRule?->start_time;
    }

    private function resolveOvertimeRuleEndTime(?WorkShift $workShift): ?string
    {
        return $workShift?->overtimeRule?->end_time;
    }

    private function minutesOfDay(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return ($hour * 60) + $minute;
    }

    private function resolveShiftEndForProfile(EmployeeProfile $profile, Carbon $dateTime): Carbon
    {
        $workShift = $this->resolveWorkShift($profile, $dateTime->copy());
        [, $shiftEnd] = $this->shiftBoundaries($dateTime->copy(), $workShift);

        return $shiftEnd;
    }

    private function isAuthorityLevelFourOrHigher(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return (int) ($user->employeeProfile?->position?->authority_level ?? 0) >= 4;
    }

    private function isAuthorityLevelFiveOrHigher(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return (int) ($user->employeeProfile?->position?->authority_level ?? 0) >= 5;
    }

    private function ensureReviewerOutranksEmployee(User $reviewer, ?EmployeeProfile $targetProfile): void
    {
        if (!$targetProfile) {
            throw new \RuntimeException('Khong tim thay ho so nhan su can duyet.');
        }

        $reviewer->loadMissing('employeeProfile.position');
        $targetProfile->loadMissing(['user', 'position']);

        if ((int) ($targetProfile->user_id ?? 0) === (int) $reviewer->id) {
            throw new \RuntimeException('Khong duoc tu duyet cham cong cua chinh minh.');
        }

        $reviewerLevel = (int) ($reviewer->employeeProfile?->position?->authority_level ?? 0);
        $targetLevel = (int) ($targetProfile->position?->authority_level ?? 0);

        if ($reviewerLevel <= $targetLevel) {
            throw new \RuntimeException('Chi nguoi co chuc vu cao hon nhan vien moi duoc duyet cham cong.');
        }
    }

    private function applyReviewerVisibilityToRecordQuery(Builder $query, ?User $viewer): void
    {
        $viewerLevel = (int) ($viewer?->employeeProfile?->position?->authority_level ?? 0);
        $viewerProfileId = (int) ($viewer?->employeeProfile?->id ?? 0);

        if ($viewerLevel <= 0) {
            $query->whereRaw('1 = 0');
            return;
        }

        if ($viewerProfileId > 0) {
            $query->where('employee_profile_id', '!=', $viewerProfileId);
        }

        $query->whereHas('employeeProfile.position', fn (Builder $positionQuery) => $positionQuery->where('authority_level', '<', $viewerLevel));
    }

    private function applyReviewerVisibilityToEmployeeQuery(Builder $query, ?User $viewer): void
    {
        $viewerLevel = (int) ($viewer?->employeeProfile?->position?->authority_level ?? 0);
        $viewerProfileId = (int) ($viewer?->employeeProfile?->id ?? 0);

        if ($viewerLevel <= 0) {
            $query->whereRaw('1 = 0');
            return;
        }

        if ($viewerProfileId > 0) {
            $query->whereKeyNot($viewerProfileId);
        }

        $query->whereHas('position', fn (Builder $positionQuery) => $positionQuery->where('authority_level', '<', $viewerLevel));
    }

    private function resolveApprovalTargetEmployeeProfile(ApprovalRequest $approvalRequest): ?EmployeeProfile
    {
        $target = $approvalRequest->target;

        if ($target instanceof AttendanceRequest || $target instanceof OvertimeRequest) {
            $target->loadMissing(['employeeProfile.user', 'employeeProfile.position']);

            return $target->employeeProfile;
        }

        $approvalRequest->loadMissing('requester.employeeProfile.position');

        return $approvalRequest->requester?->employeeProfile;
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






