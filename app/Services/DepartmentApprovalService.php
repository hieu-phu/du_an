<?php

namespace App\Services;

use App\Models\ApprovalRequest;
use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class DepartmentApprovalService extends BaseService
{
    public const REQUEST_TYPE_CREATE = 'department_create';
    public const REQUEST_TYPE_UPDATE = 'department_update';
    public const REQUEST_TYPE_TOGGLE = 'department_toggle';

    public function __construct(
        protected DepartmentService $departmentService,
        protected NotificationService $notificationService
    ) {}

    public function submitCreateRequest(array $validatedData): ApprovalRequest
    {
        return $this->createRequest(
            self::REQUEST_TYPE_CREATE,
            0,
            'Đề nghị tạo phòng ban mới',
            Arr::only($validatedData, ['name', 'description', 'manager_user_id', 'is_active'])
        );
    }

    public function submitUpdateRequest(Department $department, array $validatedData): ApprovalRequest
    {
        return $this->handleTransaction(function () use ($department, $validatedData) {
            $payload = Arr::only($validatedData, ['name', 'description', 'manager_user_id', 'is_active']);

            $request = ApprovalRequest::query()->create([
                'request_type' => self::REQUEST_TYPE_UPDATE,
                'target_type' => Department::class,
                'target_id' => $department->id,
                'requested_by' => $this->user()?->id,
                'status' => 'pending',
                'submitted_at' => now(),
                'reason' => "Đề nghị cập nhật phòng ban {$department->name}",
            ]);

            foreach ($payload as $field => $value) {
                $request->changes()->create([
                    'field_name' => $field,
                    'old_value' => json_encode($department->{$field}),
                    'new_value' => json_encode($value),
                ]);
            }

            return $request->load(['changes', 'requester']);
        });
    }

    public function submitToggleRequest(Department $department): ApprovalRequest
    {
        return $this->handleTransaction(function () use ($department) {
            $request = ApprovalRequest::query()->create([
                'request_type' => self::REQUEST_TYPE_TOGGLE,
                'target_type' => Department::class,
                'target_id' => $department->id,
                'requested_by' => $this->user()?->id,
                'status' => 'pending',
                'submitted_at' => now(),
                'reason' => $department->is_active
                    ? "Đề nghị khóa phòng ban {$department->name}"
                    : "Đề nghị mở lại phòng ban {$department->name}",
            ]);

            $request->changes()->create([
                'field_name' => 'is_active',
                'old_value' => json_encode((bool) $department->is_active),
                'new_value' => json_encode(!$department->is_active),
            ]);

            return $request->load(['changes', 'requester']);
        });
    }

    public function getRequests(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return ApprovalRequest::query()
            ->with(['requester:id,name,email', 'reviewer:id,name,email', 'changes'])
            ->whereIn('request_type', [
                self::REQUEST_TYPE_CREATE,
                self::REQUEST_TYPE_UPDATE,
                self::REQUEST_TYPE_TOGGLE,
            ])
            ->when(!empty($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate($perPage)
            ->through(function (ApprovalRequest $request) {
                $payload = $this->decodePayload($request);
                $department = $request->target_id ? Department::query()->find($request->target_id) : null;

                return [
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
                    'payload' => $this->formatPayload($payload),
                    'changes' => $this->mapChangesForDisplay($request->changes),
                    'department' => $department ? [
                        'id' => $department->id,
                        'name' => $department->name,
                        'is_active' => (bool) $department->is_active,
                    ] : null,
                ];
            });
    }

    public function getRequestStats(): array
    {
        $baseQuery = ApprovalRequest::query()
            ->whereIn('request_type', [
                self::REQUEST_TYPE_CREATE,
                self::REQUEST_TYPE_UPDATE,
                self::REQUEST_TYPE_TOGGLE,
            ]);

        return [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];
    }

    public function approve(ApprovalRequest $approvalRequest, ?string $reviewNote = null): Department
    {
        return $this->handleTransaction(function () use ($approvalRequest, $reviewNote) {
            $this->assertPendingDepartmentRequest($approvalRequest);

            $payload = $this->decodePayload($approvalRequest->loadMissing('changes'));
            $department = $this->applyApprovedRequest($approvalRequest, $payload);

            $approvalRequest->update([
                'target_id' => $department->id,
                'status' => 'approved',
                'reviewed_by' => $this->user()?->id,
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ]);

            $this->notifyRequesterDecision($approvalRequest, true, $reviewNote);

            return $department;
        });
    }

    public function reject(ApprovalRequest $approvalRequest, ?string $reviewNote = null): void
    {
        $this->handleTransaction(function () use ($approvalRequest, $reviewNote) {
            $this->assertPendingDepartmentRequest($approvalRequest);

            $approvalRequest->update([
                'status' => 'rejected',
                'reviewed_by' => $this->user()?->id,
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ]);

            $this->notifyRequesterDecision($approvalRequest, false, $reviewNote);
        });
    }

    private function createRequest(string $requestType, int $targetId, string $reason, array $payload): ApprovalRequest
    {
        return $this->handleTransaction(function () use ($requestType, $targetId, $reason, $payload) {
            $request = ApprovalRequest::query()->create([
                'request_type' => $requestType,
                'target_type' => Department::class,
                'target_id' => $targetId,
                'requested_by' => $this->user()?->id,
                'status' => 'pending',
                'submitted_at' => now(),
                'reason' => $reason,
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

    private function applyApprovedRequest(ApprovalRequest $approvalRequest, array $payload): Department
    {
        return match ($approvalRequest->request_type) {
            self::REQUEST_TYPE_CREATE => $this->approveCreate($payload),
            self::REQUEST_TYPE_UPDATE => $this->approveUpdate($approvalRequest, $payload),
            self::REQUEST_TYPE_TOGGLE => $this->approveToggle($approvalRequest, $payload),
            default => throw ValidationException::withMessages([
                'approval' => 'Loại yêu cầu phòng ban không hợp lệ.',
            ]),
        };
    }

    private function approveCreate(array $payload): Department
    {
        $this->assertUniqueName($payload['name'] ?? null);

        return $this->departmentService->store($payload);
    }

    private function approveUpdate(ApprovalRequest $approvalRequest, array $payload): Department
    {
        $department = Department::query()->findOrFail($approvalRequest->target_id);

        $this->assertUniqueName($payload['name'] ?? null, $department->id);
        $this->departmentService->update($department->id, $payload);

        return $department->fresh(['manager'])->loadCount('employeeProfiles');
    }

    private function approveToggle(ApprovalRequest $approvalRequest, array $payload): Department
    {
        $department = Department::query()->findOrFail($approvalRequest->target_id);

        $department->update([
            'is_active' => (bool) ($payload['is_active'] ?? !$department->is_active),
        ]);

        return $department->fresh(['manager'])->loadCount('employeeProfiles');
    }

    private function assertPendingDepartmentRequest(ApprovalRequest $approvalRequest): void
    {
        if (!in_array($approvalRequest->request_type, [
            self::REQUEST_TYPE_CREATE,
            self::REQUEST_TYPE_UPDATE,
            self::REQUEST_TYPE_TOGGLE,
        ], true)) {
            throw ValidationException::withMessages([
                'approval' => 'Loại yêu cầu phòng ban không hợp lệ.',
            ]);
        }

        if ($approvalRequest->status !== 'pending') {
            throw ValidationException::withMessages([
                'approval' => 'Yêu cầu này đã được xử lý.',
            ]);
        }
    }

    private function decodePayload(ApprovalRequest $approvalRequest): array
    {
        $payload = [];

        foreach ($approvalRequest->changes as $change) {
            $payload[$change->field_name] = json_decode($change->new_value, true);
        }

        return $payload;
    }

    private function formatPayload(array $payload): array
    {
        if (!empty($payload['manager_user_id'])) {
            $manager = \App\Models\User::query()->find($payload['manager_user_id']);
            $payload['manager_name'] = $manager?->name;
        } else {
            $payload['manager_name'] = null;
        }

        $payload['is_active_label'] = !empty($payload['is_active']) ? 'Đang hoạt động' : 'Tạm khóa';

        return $payload;
    }

    private function assertUniqueName(?string $name, ?int $ignoreId = null): void
    {
        if (!$name) {
            return;
        }

        $exists = Department::query()
            ->where('name', $name)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => 'Tên phòng ban đã tồn tại, không thể duyệt yêu cầu.',
            ]);
        }
    }

    private function mapChangesForDisplay(Collection $changes): array
    {
        return $changes->map(function ($change) {
            return [
                'field' => $change->field_name,
                'label' => $this->fieldLabel($change->field_name),
                'old_value' => json_decode($change->old_value ?? 'null', true),
                'new_value' => json_decode($change->new_value ?? 'null', true),
            ];
        })->values()->all();
    }

    private function fieldLabel(string $field): string
    {
        return match ($field) {
            'name' => 'Ten phong ban',
            'description' => 'Mo ta',
            'manager_user_id' => 'Truong phong',
            'is_active' => 'Trang thai',
            default => $field,
        };
    }

    private function notifyRequesterDecision(ApprovalRequest $approvalRequest, bool $approved, ?string $reviewNote = null): void
    {
        if (!$approvalRequest->requested_by) {
            return;
        }

        $reviewerName = $this->user()?->name ?? 'Admin';
        $title = $approved ? 'Yeu cau phong ban da duoc duyet' : 'Yeu cau phong ban bi tu choi';
        $decisionLabel = $approved ? 'duoc duyet' : 'bi tu choi';

        $this->notificationService->create(
            $approvalRequest->requested_by,
            $title,
            "Yeu cau {$this->requestTypeLabel($approvalRequest->request_type)} cua ban {$decisionLabel} boi {$reviewerName}.",
            [
                'approval_request_id' => $approvalRequest->id,
                'request_type' => $approvalRequest->request_type,
                'decision' => $approved ? 'approved' : 'rejected',
                'review_note' => $reviewNote,
                'action_url' => '/departments/approvals',
            ],
            '/departments/approvals',
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
            self::REQUEST_TYPE_CREATE => 'tao phong ban',
            self::REQUEST_TYPE_UPDATE => 'cap nhat phong ban',
            self::REQUEST_TYPE_TOGGLE => 'khoa/mo phong ban',
            default => 'phe duyet phong ban',
        };
    }
}
