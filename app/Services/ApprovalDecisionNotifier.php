<?php

namespace App\Services;

use App\Enums\ApprovalDecision;
use App\Enums\ApprovalDecisionDeliveryStatus;
use App\Jobs\SendApprovalDecisionEmailJob;
use App\Models\ApprovalDecisionDelivery;
use App\Models\ApprovalRequest;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRequest;
use App\Models\OvertimeRequest;
use App\Models\User;
use Carbon\Carbon;

class ApprovalDecisionNotifier
{
    public function __construct(
        protected NotificationService $notificationService,
    ) {
    }

    public function notifyApprovalRequestDecision(
        ApprovalRequest $approvalRequest,
        ApprovalDecision|string $decision,
        ?string $actionUrl = null,
        ?string $reviewNote = null,
        ?User $actor = null,
        ?string $module = null,
    ): ?ApprovalDecisionDelivery {
        $decision = $this->normalizeDecision($decision);
        $approvalRequest->loadMissing(['requester.employeeProfile', 'reviewer', 'target']);

        $recipient = $approvalRequest->requester;
        if (!$recipient) {
            return null;
        }

        $module ??= $this->moduleForApprovalRequest($approvalRequest);
        $actionUrl ??= $this->defaultActionUrlForApprovalRequest($approvalRequest);
        $decisionAt = $approvalRequest->reviewed_at ?: now();
        $itemLabel = $this->labelForApprovalRequest($approvalRequest);

        return $this->dispatchDecision([
            'module' => $module,
            'decision' => $decision,
            'recipient' => $recipient,
            'triggered_by' => $actor?->id ?? $approvalRequest->reviewed_by,
            'subject' => $this->subjectFor($itemLabel, $decision),
            'action_url' => $actionUrl,
            'reference_type' => ApprovalRequest::class,
            'reference_id' => (int) $approvalRequest->id,
            'signature' => (string) ($approvalRequest->reviewed_at?->timestamp ?? $approvalRequest->updated_at?->timestamp ?? $approvalRequest->id),
            'payload' => [
                'recipient_name' => $recipient->name,
                'item_label' => $itemLabel,
                'reviewer_name' => $actor?->name ?? $approvalRequest->reviewer?->name ?? 'Hệ thống',
                'review_note' => $reviewNote ?? $approvalRequest->review_note,
                'decision_at' => $this->formatDecisionAt($decisionAt),
                'request_type' => $approvalRequest->request_type,
            ],
        ]);
    }

    public function notifyAttendanceRecordDecision(
        AttendanceRecord $record,
        ApprovalDecision|string $decision,
        ?string $actionUrl = null,
        ?string $reviewNote = null,
        ?User $actor = null,
    ): ?ApprovalDecisionDelivery {
        $decision = $this->normalizeDecision($decision);
        $record->loadMissing(['employeeProfile.user', 'confirmer', 'rejecter']);

        $recipient = $record->employeeProfile?->user;
        if (!$recipient) {
            return null;
        }

        $workDate = $record->work_date ? Carbon::parse($record->work_date, 'Asia/Ho_Chi_Minh') : now('Asia/Ho_Chi_Minh');
        $decisionAt = $record->confirmed_at ?? $record->rejected_at ?? now();

        return $this->dispatchDecision([
            'module' => 'attendance',
            'decision' => $decision,
            'recipient' => $recipient,
            'triggered_by' => $actor?->id ?? $record->confirmed_by ?? $record->rejected_by,
            'subject' => $this->subjectFor(
                'Bản ghi công ngày ' . $workDate->format('d/m/Y'),
                $decision
            ),
            'action_url' => $actionUrl ?: sprintf('/my-attendance?month=%d&year=%d', (int) $workDate->month, (int) $workDate->year),
            'reference_type' => AttendanceRecord::class,
            'reference_id' => (int) $record->id,
            'signature' => (string) ($decisionAt?->timestamp ?? $record->updated_at?->timestamp ?? $record->id),
            'payload' => [
                'recipient_name' => $recipient->name,
                'item_label' => 'Bản ghi công ngày ' . $workDate->format('d/m/Y'),
                'reviewer_name' => $actor?->name ?? $record->confirmer?->name ?? $record->rejecter?->name ?? 'Hệ thống',
                'review_note' => $reviewNote ?? $record->approval_note,
                'decision_at' => $this->formatDecisionAt($decisionAt),
                'request_type' => 'attendance_record',
            ],
        ]);
    }

    public function notifyDirectSalaryUpdate(
        User $recipient,
        float $oldSalary,
        float $newSalary,
        ?string $reviewNote = null,
        ?User $actor = null,
    ): ?ApprovalDecisionDelivery {
        $recipient->loadMissing('employeeProfile');
        $decisionAt = now();

        return $this->dispatchDecision([
            'module' => 'salary',
            'decision' => ApprovalDecision::APPROVED,
            'recipient' => $recipient,
            'triggered_by' => $actor?->id,
            'subject' => $this->subjectFor('Cập nhật lương cơ bản', ApprovalDecision::APPROVED),
            'action_url' => '/my-salary',
            'reference_type' => User::class,
            'reference_id' => (int) $recipient->id,
            'signature' => sha1(implode('|', [(string) $recipient->id, (string) $oldSalary, (string) $newSalary, (string) $decisionAt->timestamp])),
            'payload' => [
                'recipient_name' => $recipient->name,
                'item_label' => 'Cập nhật lương cơ bản',
                'reviewer_name' => $actor?->name ?? 'Hệ thống',
                'review_note' => $reviewNote ?: sprintf(
                    'Lương cơ bản được cập nhật từ %s VND lên %s VND.',
                    number_format($oldSalary, 0, ',', '.'),
                    number_format($newSalary, 0, ',', '.')
                ),
                'decision_at' => $this->formatDecisionAt($decisionAt),
                'request_type' => 'direct_salary_update',
            ],
        ]);
    }

    private function dispatchDecision(array $attributes): ?ApprovalDecisionDelivery
    {
        /** @var User|null $recipient */
        $recipient = $attributes['recipient'] ?? null;
        if (!$recipient) {
            return null;
        }

        $module = (string) ($attributes['module'] ?? 'general');
        $decision = $attributes['decision'] instanceof ApprovalDecision
            ? $attributes['decision']
            : $this->normalizeDecision((string) $attributes['decision']);
        $subject = (string) ($attributes['subject'] ?? 'Cập nhật phê duyệt');
        $actionUrl = $attributes['action_url'] ?? null;
        $referenceType = $attributes['reference_type'] ?? null;
        $referenceId = $attributes['reference_id'] ?? null;
        $payload = (array) ($attributes['payload'] ?? []);
        $triggeredBy = $attributes['triggered_by'] ?? null;
        $signature = (string) ($attributes['signature'] ?? '');
        $recipientEmail = trim((string) $recipient->email);
        $dedupeKey = sha1(implode('|', [
            $module,
            $decision->value,
            (string) $referenceType,
            (string) $referenceId,
            (string) $recipient->id,
            $recipientEmail,
            $signature,
        ]));

        $existing = ApprovalDecisionDelivery::query()->where('dedupe_key', $dedupeKey)->first();
        if ($existing) {
            return $existing;
        }

        $mailEnabled = $this->mailEnabledForModule($module) && $recipientEmail !== '';
        $skipReason = null;
        if (!$this->moduleEnabled($module)) {
            $skipReason = 'Approval notification feature flag is disabled for this module.';
        } elseif (!$this->mailEnabledForModule($module)) {
            $skipReason = 'Approval email notifications are disabled by feature flag.';
        } elseif ($recipientEmail === '') {
            $skipReason = 'Missing recipient email.';
        }

        $delivery = ApprovalDecisionDelivery::query()->create([
            'dedupe_key' => $dedupeKey,
            'module' => $module,
            'channel' => 'mail',
            'decision' => $decision,
            'status' => $mailEnabled ? ApprovalDecisionDeliveryStatus::QUEUED : ApprovalDecisionDeliveryStatus::SKIPPED,
            'recipient_user_id' => $recipient->id,
            'recipient_email' => $recipientEmail,
            'subject' => $subject,
            'action_url' => $actionUrl,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'triggered_by' => $triggeredBy,
            'payload' => $payload,
            'queued_at' => $mailEnabled ? now() : null,
            'last_error' => $skipReason,
        ]);

        if ($this->inAppEnabled() && $this->moduleEnabled($module)) {
            $this->notificationService->create(
                $recipient->id,
                $subject,
                $this->notificationMessage($payload['item_label'] ?? 'Yêu cầu', $decision, $payload['reviewer_name'] ?? 'Hệ thống'),
                [
                    'decision' => $decision->value,
                    'action_url' => $actionUrl,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                ],
                $actionUrl,
                null,
                'approval',
                $triggeredBy,
                $referenceType,
                $referenceId,
            );
        }

        if ($mailEnabled) {
            SendApprovalDecisionEmailJob::dispatchAfterResponse($delivery->id);
        }

        return $delivery;
    }

    private function normalizeDecision(ApprovalDecision|string $decision): ApprovalDecision
    {
        return $decision instanceof ApprovalDecision
            ? $decision
            : ApprovalDecision::from((string) $decision);
    }

    private function subjectFor(string $itemLabel, ApprovalDecision $decision): string
    {
        return sprintf('[%s] %s %s', config('app.name', 'HRM'), $itemLabel, $decision->mailVerb());
    }

    private function notificationMessage(string $itemLabel, ApprovalDecision $decision, string $reviewerName): string
    {
        return sprintf('%s của bạn %s bởi %s.', $itemLabel, $decision->mailVerb(), $reviewerName);
    }

    private function defaultActionUrlForApprovalRequest(ApprovalRequest $approvalRequest): string
    {
        return match ((string) $approvalRequest->request_type) {
            'leave' => '/my-leave',
            'late_early', 'forgot_check', 'business_trip', 'make_up', 'overtime' => '/my-attendance',
            'department_create', 'department_update', 'department_toggle' => '/departments',
            'user_create', 'user_salary_change' => '/users/employee-requests',
            default => '/',
        };
    }

    private function labelForApprovalRequest(ApprovalRequest $approvalRequest): string
    {
        $target = $approvalRequest->target;

        return match ((string) $approvalRequest->request_type) {
            'leave' => 'Đơn xin nghỉ phép',
            'late_early' => 'Đơn xin đi muộn / về sớm',
            'forgot_check' => 'Đơn xin quên chấm công',
            'business_trip' => 'Đơn xin công tác',
            'make_up' => 'Đơn xin làm bù',
            'overtime' => 'Đơn tăng ca',
            'department_create' => 'Yêu cầu tạo phòng ban',
            'department_update' => 'Yêu cầu cập nhật phòng ban',
            'department_toggle' => 'Yêu cầu khóa/mở phòng ban',
            'user_create' => 'Yêu cầu tạo nhân sự',
            'user_salary_change' => 'Yêu cầu đổi lương cơ bản',
            default => $target instanceof AttendanceRequest || $target instanceof OvertimeRequest
                ? 'Yêu cầu chấm công'
                : 'Yêu cầu phê duyệt',
        };
    }

    private function moduleForApprovalRequest(ApprovalRequest $approvalRequest): string
    {
        return match ((string) $approvalRequest->request_type) {
            'department_create', 'department_update', 'department_toggle' => 'department',
            'user_create' => 'user',
            'user_salary_change' => 'salary',
            default => 'attendance',
        };
    }

    private function moduleEnabled(string $module): bool
    {
        return (bool) config('approval_notifications.enabled', true)
            && (bool) config("approval_notifications.modules.{$module}", true);
    }

    private function inAppEnabled(): bool
    {
        return (bool) config('approval_notifications.enabled', true)
            && (bool) config('approval_notifications.in_app_enabled', true);
    }

    private function mailEnabledForModule(string $module): bool
    {
        return $this->moduleEnabled($module)
            && (bool) config('approval_notifications.mail.enabled', true);
    }

    private function formatDecisionAt(Carbon|string|null $dateTime): ?string
    {
        if (!$dateTime) {
            return null;
        }

        return ($dateTime instanceof Carbon
            ? $dateTime->copy()->timezone('Asia/Ho_Chi_Minh')
            : Carbon::parse($dateTime, 'Asia/Ho_Chi_Minh')
        )->format('d/m/Y H:i');
    }
}
