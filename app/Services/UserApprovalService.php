<?php

namespace App\Services;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Position;
use App\Models\Province;
use App\Models\ApprovalRequest;
use App\Models\SalaryHistory;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class UserApprovalService extends BaseService
{
    private const REQUEST_TYPE_CREATE_USER = 'user_create';
    private const REQUEST_TYPE_SALARY_CHANGE = 'user_salary_change';

    public function __construct(
        protected UserService $userService,
        protected NotificationService $notificationService
    ) {}

    public function submitCreateRequest(array $validatedData): ApprovalRequest
    {
        return $this->handleTransaction(function () use ($validatedData) {
            $payload = Arr::except($validatedData, ['avatar', 'password_confirmation']);

            $request = ApprovalRequest::query()->create([
                'request_type' => self::REQUEST_TYPE_CREATE_USER,
                'target_type' => User::class,
                'target_id' => 0,
                'requested_by' => $this->user()?->id,
                'status' => 'pending',
                'submitted_at' => now(),
                'reason' => 'De nghi tao tai khoan nhan su moi',
            ]);

            foreach ($payload as $field => $value) {
                $request->changes()->create([
                    'field_name' => $field,
                    'old_value' => null,
                    'new_value' => json_encode($value),
                ]);
            }

            return $request->load(['changes', 'requester']);
        });
    }

    public function submitSalaryChangeRequest(User $targetUser, float $newSalary, ?string $reason = null): ApprovalRequest
    {
        return $this->handleTransaction(function () use ($targetUser, $newSalary, $reason) {
            $targetUser->loadMissing('employeeProfile');
            $profile = $targetUser->employeeProfile;

            if (!$profile) {
                throw ValidationException::withMessages([
                    'approval' => 'Nhan su nay chua co ho so nhan vien de thay doi luong.',
                ]);
            }

            $oldSalary = (float) ($profile->base_salary ?? 0);
            $normalizedSalary = round($newSalary, 2);

            if ($normalizedSalary === $oldSalary) {
                throw ValidationException::withMessages([
                    'base_salary' => 'Luong moi trung voi luong hien tai, khong can gui yeu cau.',
                ]);
            }

            $hasPendingRequest = ApprovalRequest::query()
                ->where('request_type', self::REQUEST_TYPE_SALARY_CHANGE)
                ->where('target_type', User::class)
                ->where('target_id', $targetUser->id)
                ->where('status', 'pending')
                ->exists();

            if ($hasPendingRequest) {
                throw ValidationException::withMessages([
                    'base_salary' => 'Nhan su nay dang co yeu cau doi luong cho duyet.',
                ]);
            }

            $request = ApprovalRequest::query()->create([
                'request_type' => self::REQUEST_TYPE_SALARY_CHANGE,
                'target_type' => User::class,
                'target_id' => $targetUser->id,
                'requested_by' => $this->user()?->id,
                'status' => 'pending',
                'submitted_at' => now(),
                'reason' => $reason ?: 'De nghi thay doi luong co ban',
            ]);

            $payload = [
                'user_id' => $targetUser->id,
                'employee_profile_id' => $profile->id,
                'employee_name' => $targetUser->name,
                'employee_email' => $targetUser->email,
                'employee_code' => $profile->employee_code,
                'old_salary' => $oldSalary,
                'new_salary' => $normalizedSalary,
                'currency' => 'VND',
                'effective_date' => now()->toDateString(),
                'request_reason' => $reason,
            ];

            foreach ($payload as $field => $value) {
                $oldValue = null;
                if ($field === 'new_salary') {
                    $oldValue = $oldSalary;
                }

                $request->changes()->create([
                    'field_name' => $field,
                    'old_value' => json_encode($oldValue),
                    'new_value' => json_encode($value),
                ]);
            }

            return $request->load(['changes', 'requester']);
        });
    }

    public function getApprovalRequests(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return ApprovalRequest::query()
            ->with(['requester:id,name,email', 'reviewer:id,name,email', 'changes'])
            ->whereIn('request_type', [self::REQUEST_TYPE_CREATE_USER, self::REQUEST_TYPE_SALARY_CHANGE])
            ->when(!empty($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(!empty($filters['request_type']), fn ($query) => $query->where('request_type', $filters['request_type']))
            ->latest()
            ->paginate($perPage)
            ->through(fn (ApprovalRequest $request) => [
                'id' => $request->id,
                'request_type' => $request->request_type,
                'status' => $request->status,
                'reason' => $request->reason,
                'review_note' => $request->review_note,
                'submitted_at' => optional($request->submitted_at)->toDateTimeString(),
                'reviewed_at' => optional($request->reviewed_at)->toDateTimeString(),
                'requested_by' => $request->requester ? [
                    'id' => $request->requester->id,
                    'name' => $request->requester->name,
                    'email' => $request->requester->email,
                ] : null,
                'reviewed_by' => $request->reviewer ? [
                    'id' => $request->reviewer->id,
                    'name' => $request->reviewer->name,
                    'email' => $request->reviewer->email,
                ] : null,
                'payload' => $this->formatPayloadForList($this->decodePayload($request)),
                'changes' => $this->mapChangesForDisplay($request->changes),
            ]);
    }

    public function getApprovalStats(): array
    {
        $baseQuery = ApprovalRequest::query()
            ->whereIn('request_type', [self::REQUEST_TYPE_CREATE_USER, self::REQUEST_TYPE_SALARY_CHANGE]);

        return [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];
    }

    public function getRequesterStatusSummary(int $requesterId): array
    {
        $requests = ApprovalRequest::query()
            ->with(['reviewer:id,name,email', 'changes'])
            ->whereIn('request_type', [self::REQUEST_TYPE_CREATE_USER, self::REQUEST_TYPE_SALARY_CHANGE])
            ->where('requested_by', $requesterId)
            ->latest()
            ->get()
            ->map(function (ApprovalRequest $request) {
                return [
                    'id' => $request->id,
                    'request_type' => $request->request_type,
                    'status' => $request->status,
                    'submitted_at' => optional($request->submitted_at)->toDateTimeString(),
                    'reviewed_at' => optional($request->reviewed_at)->toDateTimeString(),
                    'review_note' => $request->review_note,
                    'reviewed_by' => $request->reviewer ? [
                        'id' => $request->reviewer->id,
                        'name' => $request->reviewer->name,
                        'email' => $request->reviewer->email,
                    ] : null,
                    'payload' => $this->formatPayloadForList($this->decodePayload($request)),
                    'changes' => $this->mapChangesForDisplay($request->changes),
                ];
            });

        return [
            'approved' => $requests->where('status', 'approved')->values()->all(),
            'pending' => $requests->where('status', 'pending')->values()->all(),
            'rejected' => $requests->where('status', 'rejected')->values()->all(),
        ];
    }

    public function approve(ApprovalRequest $approvalRequest, ?string $reviewNote = null): User
    {
        return $this->handleTransaction(function () use ($approvalRequest, $reviewNote) {
            $this->assertPendingApprovalRequest($approvalRequest);
            $payload = $this->decodePayload($approvalRequest->loadMissing('changes'));

            if ($approvalRequest->request_type === self::REQUEST_TYPE_CREATE_USER) {
                $this->assertUniqueCredentials($payload);

                $user = $this->userService->createUser($payload);

                $approvalRequest->update([
                    'target_id' => $user->id,
                    'status' => 'approved',
                    'reviewed_by' => $this->user()?->id,
                    'reviewed_at' => now(),
                    'review_note' => $reviewNote,
                ]);

                $this->notifyRequesterDecision($approvalRequest, true, $reviewNote);

                return $user;
            }

            if ($approvalRequest->request_type === self::REQUEST_TYPE_SALARY_CHANGE) {
                $user = User::query()->findOrFail((int) ($payload['user_id'] ?? $approvalRequest->target_id));
                $user->loadMissing('employeeProfile');
                $profile = $user->employeeProfile;

                if (!$profile) {
                    throw ValidationException::withMessages([
                        'approval' => 'Nhan su khong co ho so de cap nhat luong.',
                    ]);
                }

                $oldSalary = (float) ($profile->base_salary ?? 0);
                $newSalary = (float) ($payload['new_salary'] ?? $oldSalary);

                $profile->update([
                    'base_salary' => $newSalary,
                ]);

                SalaryHistory::query()->create([
                    'employee_profile_id' => $profile->id,
                    'old_salary' => $oldSalary,
                    'new_salary' => $newSalary,
                    'currency' => (string) ($payload['currency'] ?? 'VND'),
                    'effective_date' => (string) ($payload['effective_date'] ?? now()->toDateString()),
                    'approved_by' => $this->user()?->id,
                    'note' => $reviewNote ?: ($payload['request_reason'] ?? null),
                ]);

                $approvalRequest->update([
                    'target_id' => $user->id,
                    'status' => 'approved',
                    'reviewed_by' => $this->user()?->id,
                    'reviewed_at' => now(),
                    'review_note' => $reviewNote,
                ]);

                $this->notifyRequesterDecision($approvalRequest, true, $reviewNote);

                return $user;
            }

            throw ValidationException::withMessages([
                'approval' => 'Loai yeu cau khong hop le.',
            ]);
        });
    }

    public function reject(ApprovalRequest $approvalRequest, ?string $reviewNote = null): void
    {
        $this->handleTransaction(function () use ($approvalRequest, $reviewNote) {
            if ($approvalRequest->status !== 'pending') {
                throw ValidationException::withMessages([
                    'approval' => 'Yeu cau nay da duoc xu ly.',
                ]);
            }

            $approvalRequest->update([
                'status' => 'rejected',
                'reviewed_by' => $this->user()?->id,
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ]);

            $this->notifyRequesterDecision($approvalRequest, false, $reviewNote);
        });
    }

    private function decodePayload(ApprovalRequest $approvalRequest): array
    {
        $payload = [];

        foreach ($approvalRequest->changes as $change) {
            $payload[$change->field_name] = json_decode($change->new_value, true);
        }

        return $payload;
    }

    private function assertUniqueCredentials(array $payload): void
    {
        if (!empty($payload['email']) && User::query()->where('email', $payload['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email da ton tai, khong the duyet yeu cau.',
            ]);
        }

        if (!empty($payload['phone']) && User::query()->where('phone', $payload['phone'])->exists()) {
            throw ValidationException::withMessages([
                'phone' => 'So dien thoai da ton tai, khong the duyet yeu cau.',
            ]);
        }
    }

    private function formatPayloadForList(array $payload): array
    {
        $payload['role_label'] = match ($payload['role_name'] ?? null) {
            'admin' => 'Admin',
            'hr' => 'HR',
            'employee' => 'Nhan vien',
            default => '-',
        };

        $payload['department_name'] = !empty($payload['department_id'])
            ? Department::query()->whereKey($payload['department_id'])->value('name')
            : null;

        $payload['position_name'] = !empty($payload['position_id'])
            ? Position::query()->whereKey($payload['position_id'])->value('name')
            : null;

        $payload['province_name'] = !empty($payload['province_id'])
            ? Province::query()->whereKey($payload['province_id'])->value('name')
            : null;

        $payload['ward_name'] = !empty($payload['ward_id'])
            ? Ward::query()->whereKey($payload['ward_id'])->value('name')
            : null;

        $payload['old_salary'] = isset($payload['old_salary']) ? (float) $payload['old_salary'] : null;
        $payload['new_salary'] = isset($payload['new_salary']) ? (float) $payload['new_salary'] : null;

        if (!empty($payload['employee_profile_id'])) {
            $employeeProfile = EmployeeProfile::query()
                ->with('department:id,name')
                ->find($payload['employee_profile_id']);

            $payload['department_name'] = $payload['department_name']
                ?? $employeeProfile?->department?->name;
        }

        return $payload;
    }

    private function assertPendingApprovalRequest(ApprovalRequest $approvalRequest): void
    {
        if (!in_array($approvalRequest->request_type, [self::REQUEST_TYPE_CREATE_USER, self::REQUEST_TYPE_SALARY_CHANGE], true)) {
            throw ValidationException::withMessages([
                'approval' => 'Loai yeu cau khong hop le.',
            ]);
        }

        if ($approvalRequest->status !== 'pending') {
            throw ValidationException::withMessages([
                'approval' => 'Yeu cau nay da duoc xu ly.',
            ]);
        }
    }

    private function mapChangesForDisplay(Collection $changes): array
    {
        return $changes->map(function ($change) {
            $oldValue = json_decode($change->old_value ?? 'null', true);
            $newValue = json_decode($change->new_value ?? 'null', true);

            return [
                'field' => $change->field_name,
                'label' => $this->fieldLabel($change->field_name),
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ];
        })->values()->all();
    }

    private function fieldLabel(string $field): string
    {
        return match ($field) {
            'user_id' => 'ID tài khoản',
            'employee_profile_id' => 'ID hồ sơ nhân sự',
            'employee_name' => 'Nhân sự',
            'employee_email' => 'Email nhân sự',
            'employee_code' => 'Mã nhân sự',
            'name' => 'Họ tên',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'department_id' => 'Phòng ban',
            'position_id' => 'Chức vụ',
            'base_salary', 'new_salary' => 'Lương cơ bản',
            'old_salary' => 'Lương hiện tại',
            'currency' => 'Đơn vị',
            'effective_date' => 'Ngày hiệu lực',
            'request_reason' => 'Lý do',
            default => $field,
        };
    }

    private function notifyRequesterDecision(ApprovalRequest $approvalRequest, bool $approved, ?string $reviewNote = null): void
    {
        if (!$approvalRequest->requested_by) {
            return;
        }

        $approvalRequest->loadMissing('requester:id,name');
        $reviewerName = $this->user()?->name ?? 'Admin';
        $decisionLabel = $approved ? 'duoc duyet' : 'bi tu choi';
        $title = $approved ? 'Yeu cau da duoc duyet' : 'Yeu cau bi tu choi';

        $this->notificationService->create(
            $approvalRequest->requested_by,
            $title,
            "Yeu cau {$this->requestTypeLabel($approvalRequest->request_type)} cua ban {$decisionLabel} boi {$reviewerName}.",
            [
                'approval_request_id' => $approvalRequest->id,
                'request_type' => $approvalRequest->request_type,
                'decision' => $approved ? 'approved' : 'rejected',
                'review_note' => $reviewNote,
                'action_url' => '/users/employee-requests',
            ],
            '/users/employee-requests',
            null,
            'approval',
            $this->user()?->id,
            ApprovalRequest::class,
            $approvalRequest->id
        );
    }

    private function requestTypeLabel(string $requestType): string
    {
        return match ($requestType) {
            self::REQUEST_TYPE_CREATE_USER => 'tao tai khoan',
            self::REQUEST_TYPE_SALARY_CHANGE => 'doi luong co ban',
            default => 'phe duyet',
        };
    }
}
