<?php

namespace App\Services;

use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeProfile;
use App\Models\LeaveBalanceTransaction;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LeaveManagementService extends BaseService
{
    public function getManagementData(array $filters = []): array
    {
        $year = max(2000, min(2100, (int) ($filters['year'] ?? now()->year)));
        $leaveTypeId = filled($filters['leave_type_id'] ?? null) ? (int) $filters['leave_type_id'] : null;
        $employeeProfileId = filled($filters['employee_profile_id'] ?? null) ? (int) $filters['employee_profile_id'] : null;

        $balanceQuery = EmployeeLeaveBalance::query()
            ->with(['employeeProfile.user:id,name', 'employeeProfile.department:id,name', 'leaveType:id,code,name,is_paid,deducts_balance'])
            ->where('year', $year)
            ->orderByDesc('id');

        if ($leaveTypeId) {
            $balanceQuery->where('leave_type_id', $leaveTypeId);
        }

        if ($employeeProfileId) {
            $balanceQuery->where('employee_profile_id', $employeeProfileId);
        }

        $balances = $balanceQuery->get();

        return [
            'filters' => [
                'year' => $year,
                'leave_type_id' => $leaveTypeId,
                'employee_profile_id' => $employeeProfileId,
            ],
            'leave_types' => $this->leaveTypeOptions(false),
            'active_leave_types' => $this->leaveTypeOptions(true),
            'employees' => $this->employeeOptions(),
            'balances' => $balances->map(fn (EmployeeLeaveBalance $balance) => $this->transformBalance($balance))->values(),
            'summary' => [
                'total_entitled' => round((float) $balances->sum(fn (EmployeeLeaveBalance $item) => (float) $item->opening_balance + (float) $item->accrued_days), 2),
                'total_used' => round((float) $balances->sum(fn (EmployeeLeaveBalance $item) => (float) $item->used_days), 2),
                'total_pending' => round((float) $balances->sum(fn (EmployeeLeaveBalance $item) => (float) $item->pending_days), 2),
                'total_available' => round((float) $balances->sum(fn (EmployeeLeaveBalance $item) => $item->available_days), 2),
            ],
            'recent_transactions' => LeaveBalanceTransaction::query()
                ->with(['balance.employeeProfile.user:id,name', 'balance.leaveType:id,name', 'creator:id,name'])
                ->latest('id')
                ->limit(80)
                ->get()
                ->map(fn (LeaveBalanceTransaction $transaction) => [
                    'id' => $transaction->id,
                    'employee_name' => $transaction->balance?->employeeProfile?->user?->name,
                    'leave_type_name' => $transaction->balance?->leaveType?->name,
                    'type' => $transaction->type,
                    'days' => (float) $transaction->days,
                    'balance_after' => $transaction->balance_after !== null ? (float) $transaction->balance_after : null,
                    'note' => $transaction->note,
                    'created_by_name' => $transaction->creator?->name,
                    'created_at' => optional($transaction->created_at)->format('Y-m-d H:i:s'),
                ])
                ->values(),
        ];
    }

    public function storeLeaveType(array $payload): LeaveType
    {
        return $this->handleTransaction(function () use ($payload) {
            $leaveType = LeaveType::query()->create($this->leaveTypePayload($payload));
            $this->audit('leave', 'create_leave_type', "Create leave type #{$leaveType->id}", 'leave_types', $leaveType->id);

            return $leaveType;
        });
    }

    public function updateLeaveType(LeaveType $leaveType, array $payload): LeaveType
    {
        return $this->handleTransaction(function () use ($leaveType, $payload) {
            $leaveType->update($this->leaveTypePayload($payload, $leaveType));
            $this->audit('leave', 'update_leave_type', "Update leave type #{$leaveType->id}", 'leave_types', $leaveType->id);

            return $leaveType;
        });
    }

    public function toggleLeaveType(LeaveType $leaveType): LeaveType
    {
        return $this->handleTransaction(function () use ($leaveType) {
            $leaveType->update(['is_active' => !$leaveType->is_active]);
            $this->audit('leave', 'toggle_leave_type', "Toggle leave type #{$leaveType->id}", 'leave_types', $leaveType->id);

            return $leaveType;
        });
    }

    public function grantBalance(User $actor, array $payload): EmployeeLeaveBalance
    {
        return $this->handleTransaction(function () use ($actor, $payload) {
            $leaveType = LeaveType::query()->findOrFail((int) $payload['leave_type_id']);
            $balance = $this->ensureBalance(
                (int) $payload['employee_profile_id'],
                (int) $leaveType->id,
                (int) $payload['year']
            );

            $balance->update($this->automaticBalancePayload(
                EmployeeProfile::query()->findOrFail((int) $payload['employee_profile_id']),
                $leaveType,
                (int) $payload['year']
            ));

            $this->recordTransaction($balance->fresh(), null, 'grant', 0, $actor, trim((string) ($payload['note'] ?? 'Cap quy phep nam tu han muc loai nghi')));
            $this->audit('leave', 'grant_leave_balance', "Grant leave balance #{$balance->id}", 'employee_leave_balances', $balance->id);

            return $balance->fresh(['employeeProfile.user', 'leaveType']);
        });
    }

    public function grantBulk(User $actor, array $payload): int
    {
        return $this->handleTransaction(function () use ($actor, $payload) {
            $year = (int) $payload['year'];
            $leaveTypes = LeaveType::query()
                ->where('is_active', true)
                ->when(filled($payload['leave_type_id'] ?? null), fn (Builder $query) => $query->whereKey((int) $payload['leave_type_id']))
                ->get();

            $profiles = EmployeeProfile::query()
                ->where('employment_status', 'active')
                ->when(filled($payload['employee_profile_id'] ?? null), fn (Builder $query) => $query->whereKey((int) $payload['employee_profile_id']))
                ->get();

            $count = 0;
            foreach ($profiles as $profile) {
                foreach ($leaveTypes as $leaveType) {
                    $balance = $this->ensureBalance((int) $profile->id, (int) $leaveType->id, $year);
                    $balance->update($this->automaticBalancePayload($profile, $leaveType, $year));
                    $this->recordTransaction($balance->fresh(), null, 'grant', 0, $actor, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ');
                    $count++;
                }
            }

            $this->audit('leave', 'bulk_grant_leave_balance', "Bulk grant leave balances {$count} rows for {$year}", null, null);

            return $count;
        });
    }

    public function adjustBalance(User $actor, EmployeeLeaveBalance $balance, array $payload): EmployeeLeaveBalance
    {
        return $this->handleTransaction(function () use ($actor, $balance, $payload) {
            $days = (float) ($payload['days'] ?? 0);

            if (abs($days) <= 0) {
                throw ValidationException::withMessages(['days' => 'Số ngày điều chỉnh phải khác 0.']);
            }

            $balance->adjusted_days = (float) $balance->adjusted_days + $days;
            $balance->save();

            $this->recordTransaction($balance->fresh(), null, 'adjust', $days, $actor, trim((string) ($payload['note'] ?? 'Điều chỉnh quỹ phép')));
            $this->audit('leave', 'adjust_leave_balance', "Adjust leave balance #{$balance->id}", 'employee_leave_balances', $balance->id);

            return $balance->fresh(['employeeProfile.user', 'leaveType']);
        });
    }

    public function reserveForRequest(User $actor, int $employeeProfileId, LeaveType $leaveType, int $year, int $attendanceRequestId, float $days): void
    {
        if (!$leaveType->deducts_balance || $days <= 0) {
            return;
        }

        $balance = $this->ensureBalance($employeeProfileId, (int) $leaveType->id, $year);

        if ($balance->available_days < $days) {
            throw ValidationException::withMessages([
                'leave_type_id' => 'Số dư phép không đủ để gửi đơn.',
            ]);
        }

        $balance->pending_days = (float) $balance->pending_days + $days;
        $balance->save();
        $this->recordTransaction($balance->fresh(), $attendanceRequestId, 'pending', $days, $actor, 'Giữ phép cho đơn chờ duyệt');
    }

    public function finalizeRequest(User $actor, \App\Models\AttendanceRequest $request, string $decision): void
    {
        if ($request->request_type !== 'leave' || !$request->leave_type_id || (float) $request->leave_days <= 0) {
            return;
        }

        $request->loadMissing('leaveType');
        $leaveType = $request->leaveType;

        if (!$leaveType?->deducts_balance) {
            return;
        }

        $year = (int) optional($request->from_date ?? $request->request_date)->format('Y') ?: (int) now()->year;
        $balance = $this->ensureBalance((int) $request->employee_profile_id, (int) $leaveType->id, $year);
        $days = (float) $request->leave_days;
        $balance->pending_days = max(0, (float) $balance->pending_days - $days);

        if ($decision === 'approved') {
            $balance->used_days = (float) $balance->used_days + $days;
            $type = 'approve';
            $note = 'Duyệt đơn nghỉ phép';
        } elseif ($decision === 'cancelled') {
            $type = 'reject';
            $note = 'Hoàn phép do hủy đơn';
        } else {
            $type = 'reject';
            $note = 'Hoàn phép do từ chối đơn';
        }

        $balance->save();
        $this->recordTransaction($balance->fresh(), (int) $request->id, $type, $days, $actor, $note);
    }

    public function leaveTypeOptions(bool $activeOnly = true): array
    {
        return LeaveType::query()
            ->when($activeOnly, fn (Builder $query) => $query->where('is_active', true))
            ->orderBy('name')
            ->get()
            ->map(fn (LeaveType $type) => [
                'id' => $type->id,
                'code' => $type->code,
                'name' => $type->name,
                'is_paid' => (bool) $type->is_paid,
                'deducts_balance' => (bool) $type->deducts_balance,
                'requires_attachment' => (bool) $type->requires_attachment,
                'annual_quota' => (float) $type->annual_quota,
                'prorate_by_hire_date' => (bool) $type->prorate_by_hire_date,
                'max_days_per_request' => $type->max_days_per_request !== null ? (float) $type->max_days_per_request : null,
                'carryover_limit' => null,
                'description' => $type->description,
                'is_active' => (bool) $type->is_active,
            ])
            ->values()
            ->all();
    }

    public function employeeOptions(): array
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

    public function ensureBalance(int $employeeProfileId, int $leaveTypeId, int $year): EmployeeLeaveBalance
    {
        $leaveType = LeaveType::query()->findOrFail($leaveTypeId);

        return EmployeeLeaveBalance::query()->firstOrCreate(
            [
                'employee_profile_id' => $employeeProfileId,
                'leave_type_id' => $leaveTypeId,
                'year' => $year,
            ],
            [
                'opening_balance' => 0,
                'accrued_days' => (float) $leaveType->annual_quota,
                'used_days' => 0,
                'pending_days' => 0,
                'adjusted_days' => 0,
            ]
        );
    }

    private function automaticBalancePayload(EmployeeProfile $profile, LeaveType $leaveType, int $year): array
    {
        return [
            'opening_balance' => 0,
            'accrued_days' => $this->calculateAnnualAccrual($profile, $leaveType, $year),
            'carryover_expires_on' => null,
        ];
    }

    private function calculateAnnualAccrual(EmployeeProfile $profile, LeaveType $leaveType, int $year): float
    {
        $baseQuota = (float) $leaveType->annual_quota;
        
        // Seniority leave: +1 day for every 5 years (only for ANNUAL leave type)
        $seniorityDays = 0;
        if ($leaveType->code === 'ANNUAL' && $profile->hire_date) {
            // Calculate tenure in years relative to the target year's start
            $targetYearStart = now()->setYear($year)->startOfYear();
            $tenureYears = $profile->hire_date->diffInYears($targetYearStart);
            $seniorityDays = (float) floor($tenureYears / 5);
        }

        $totalAnnualQuota = $baseQuota + $seniorityDays;

        if (!$leaveType->prorate_by_hire_date || !$profile->hire_date || (int) $profile->hire_date->year < $year) {
            return round($totalAnnualQuota, 2);
        }

        if ((int) $profile->hire_date->year > $year) {
            return 0.0;
        }

        // Pro-rate by months worked in the first year (e.g. hire date month 4 means 9 months worked)
        $months = 13 - (int) $profile->hire_date->month;

        return round($totalAnnualQuota * max(0, $months) / 12, 2);
    }

    public function transformBalance(EmployeeLeaveBalance $balance): array
    {
        return [
            'id' => $balance->id,
            'employee_profile_id' => $balance->employee_profile_id,
            'employee_name' => $balance->employeeProfile?->user?->name,
            'employee_code' => $balance->employeeProfile?->employee_code,
            'department_name' => $balance->employeeProfile?->department?->name,
            'leave_type_id' => $balance->leave_type_id,
            'leave_type_name' => $balance->leaveType?->name,
            'leave_type_paid' => (bool) $balance->leaveType?->is_paid,
            'year' => (int) $balance->year,
            'opening_balance' => (float) $balance->opening_balance,
            'accrued_days' => (float) $balance->accrued_days,
            'used_days' => (float) $balance->used_days,
            'pending_days' => (float) $balance->pending_days,
            'adjusted_days' => (float) $balance->adjusted_days,
            'carryover_expires_on' => optional($balance->carryover_expires_on)->format('Y-m-d'),
            'total_entitled' => round($balance->total_entitled, 2),
            'available_days' => round($balance->available_days, 2),
        ];
    }

    private function leaveTypePayload(array $payload, ?LeaveType $leaveType = null): array
    {
        $name = trim((string) ($payload['name'] ?? ''));
        $code = $this->generateLeaveTypeCode((string) ($payload['code'] ?? ''), $name, $leaveType);

        return [
            'code' => $code,
            'name' => $name,
            'is_paid' => (bool) ($payload['is_paid'] ?? true),
            'deducts_balance' => (bool) ($payload['deducts_balance'] ?? true),
            'requires_attachment' => (bool) ($payload['requires_attachment'] ?? false),
            'annual_quota' => (float) ($payload['annual_quota'] ?? 0),
            'prorate_by_hire_date' => (bool) ($payload['prorate_by_hire_date'] ?? true),
            'max_days_per_request' => filled($payload['max_days_per_request'] ?? null) ? (float) $payload['max_days_per_request'] : null,
            'carryover_limit' => null,
            'description' => trim((string) ($payload['description'] ?? '')) ?: null,
            'is_active' => (bool) ($payload['is_active'] ?? true),
        ];
    }

    private function generateLeaveTypeCode(string $providedCode, string $name, ?LeaveType $leaveType = null): string
    {
        $baseCode = Str::of($providedCode !== '' ? $providedCode : $name)
            ->ascii()
            ->lower()
            ->slug('_')
            ->upper()
            ->toString();

        if ($baseCode === '') {
            $baseCode = 'LEAVE_TYPE';
        }

        $code = Str::limit($baseCode, 30, '');
        $suffix = 2;

        while (
            LeaveType::query()
                ->when($leaveType?->id, fn (Builder $query) => $query->whereKeyNot($leaveType->id))
                ->where('code', $code)
                ->exists()
        ) {
            $suffixLabel = '_' . $suffix;
            $trimmedBase = Str::limit($baseCode, 30 - strlen($suffixLabel), '');
            $code = $trimmedBase . $suffixLabel;
            $suffix++;
        }

        return $code;
    }

    private function recordTransaction(EmployeeLeaveBalance $balance, ?int $attendanceRequestId, string $type, float $days, User $actor, ?string $note): void
    {
        LeaveBalanceTransaction::query()->create([
            'employee_leave_balance_id' => $balance->id,
            'attendance_request_id' => $attendanceRequestId,
            'type' => $type,
            'days' => $days,
            'balance_after' => $balance->available_days,
            'note' => $note,
            'created_by' => $actor->id,
        ]);
    }
}


