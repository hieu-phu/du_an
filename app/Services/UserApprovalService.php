<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Position;
use App\Models\Province;
use App\Models\ApprovalRequest;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class UserApprovalService extends BaseService
{
    private const REQUEST_TYPE_CREATE_USER = 'user_create';

    public function __construct(
        protected UserService $userService
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

    public function getCreateUserRequests(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return ApprovalRequest::query()
            ->with(['requester:id,name,email', 'reviewer:id,name,email', 'changes'])
            ->where('request_type', self::REQUEST_TYPE_CREATE_USER)
            ->when(!empty($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate($perPage)
            ->through(fn (ApprovalRequest $request) => [
                'id' => $request->id,
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
            ]);
    }

    public function getRequesterStatusSummary(int $requesterId): array
    {
        $requests = ApprovalRequest::query()
            ->with(['reviewer:id,name,email', 'changes'])
            ->where('request_type', self::REQUEST_TYPE_CREATE_USER)
            ->where('requested_by', $requesterId)
            ->latest()
            ->get()
            ->map(function (ApprovalRequest $request) {
                return [
                    'id' => $request->id,
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
            if ($approvalRequest->request_type !== self::REQUEST_TYPE_CREATE_USER) {
                throw ValidationException::withMessages([
                    'approval' => 'Loai yeu cau khong hop le.',
                ]);
            }

            if ($approvalRequest->status !== 'pending') {
                throw ValidationException::withMessages([
                    'approval' => 'Yeu cau nay da duoc xu ly.',
                ]);
            }

            $payload = $this->decodePayload($approvalRequest->loadMissing('changes'));

            $this->assertUniqueCredentials($payload);

            $user = $this->userService->createUser($payload);

            $approvalRequest->update([
                'target_id' => $user->id,
                'status' => 'approved',
                'reviewed_by' => $this->user()?->id,
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ]);

            return $user;
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

        return $payload;
    }
}
