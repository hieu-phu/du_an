<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\FeedbackEscalation;
use App\Models\FeedbackMessage;
use App\Models\Position;
use App\Models\User;
use App\Support\PositionCapability;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class FeedbackEscalationService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function receiverOptions(User $sender): array
    {
        return $this->directSuperiorFeedbackPositions($sender)
            ->map(fn (Position $position) => [
                'value' => $position->id,
                'label' => "{$position->name} (Rank {$position->authority_level})",
            ])
            ->values()
            ->all();
    }

    public function canSendToPosition(User $sender, Position $receiverPosition): bool
    {
        return $this->directSuperiorFeedbackPositions($sender)
            ->contains(fn (Position $position) => (int) $position->id === (int) $receiverPosition->id);
    }

    public function recipientsForPosition(Position $position, User $sender): Collection
    {
        return User::query()
            ->where('status', 'active')
            ->whereKeyNot($sender->id)
            ->with('employeeProfile.position')
            ->get(['id', 'name', 'email'])
            ->filter(fn (User $candidate) => $this->canReceiveFeedbackForPosition($candidate, (int) $position->id))
            ->values();
    }

    public function canReceiveFeedbackForPosition(User $user, int $positionId): bool
    {
        return (int) ($user->employeeProfile?->position_id ?? 0) === $positionId
            && $this->canProcessFeedback($user);
    }

    public function canReplyFeedbackForPosition(User $user, int $positionId): bool
    {
        return $this->canReceiveFeedbackForPosition($user, $positionId)
            && $user->hasAnyPositionCapability([
                PositionCapability::REPLY_FEEDBACK,
                PositionCapability::MANAGE_FEEDBACKS,
            ]);
    }

    public function triggerAutoEscalationSweep(): int
    {
        $intervalMinutes = max(1, (int) config('feedback.auto_escalation_check_interval_minutes', 5));
        $hours = max(1, (int) config('feedback.escalation_hours', 24));
        $cacheKey = 'feedback:auto-escalation:last-check';

        if (!Cache::add($cacheKey, now()->timestamp, now()->addMinutes($intervalMinutes))) {
            return 0;
        }

        return $this->escalateStaleFeedbacks($hours);
    }

    public function escalateStaleFeedbacks(int $hours = 24): int
    {
        $cutoff = now()->subHours($hours);
        $escalated = 0;

        FeedbackMessage::query()
            ->with(['sender.employeeProfile.position', 'receiverPosition'])
            ->whereNotIn('conversation_status', ['resolved', 'closed'])
            ->where('waiting_for', 'handler')
            ->whereNotNull('receiver_position_id')
            ->where(function ($query) use ($cutoff): void {
                $query->where(function ($subQuery) use ($cutoff): void {
                    $subQuery->whereNull('last_escalated_at')
                        ->where('created_at', '<=', $cutoff);
                })->orWhere('last_escalated_at', '<=', $cutoff);
            })
            ->orderBy('id')
            ->chunkById(100, function ($feedbacks) use (&$escalated): void {
                foreach ($feedbacks as $feedback) {
                    if ($this->escalateFeedback($feedback)) {
                        $escalated++;
                    }
                }
            });

        return $escalated;
    }

    private function escalateFeedback(FeedbackMessage $feedback): bool
    {
        $sender = $feedback->sender;
        $currentPosition = $feedback->receiverPosition;

        if (!$sender || !$currentPosition) {
            return false;
        }

        $nextPosition = $this->nextSuperiorFeedbackPositions($sender, (int) $currentPosition->authority_level)
            ->first();

        if (!$nextPosition) {
            return false;
        }

        $nextEscalationCount = (int) ($feedback->escalation_count ?? 0) + 1;

        $feedback->update([
            'receiver_position_id' => $nextPosition->id,
            'last_escalated_at' => now(),
            'escalation_count' => $nextEscalationCount,
            'status' => 'sent',
            'conversation_status' => 'waiting_handler',
            'waiting_for' => 'handler',
            'read_at' => null,
        ]);

        FeedbackEscalation::query()->create([
            'feedback_message_id' => $feedback->id,
            'from_position_id' => $currentPosition->id,
            'to_position_id' => $nextPosition->id,
            'escalation_count' => $nextEscalationCount,
            'escalated_at' => now(),
            'reason' => 'Timeout: feedback unreplied past escalation threshold',
        ]);

        $this->notifyEscalation($feedback->fresh(['sender', 'receiverPosition']), $sender, $nextPosition);

        return true;
    }

    private function notifyEscalation(FeedbackMessage $feedback, User $sender, Position $nextPosition): void
    {
        $recipients = $this->recipientsForPosition($nextPosition, $sender);
        $recipientIds = $recipients->pluck('id')->all();

        if (!empty($recipientIds)) {
            $this->notificationService->createForUsers(
                $recipientIds,
                'Feedback qua han da leo thang',
                "Feedback '{$feedback->subject}' cua {$sender->name} da qua han va duoc chuyen len {$nextPosition->name}.",
                [
                    'feedback_message_id' => $feedback->id,
                    'action_url' => '/feedbacks',
                    'escalation_count' => (int) ($feedback->escalation_count ?? 0),
                ],
                '/feedbacks',
                null,
                'feedback',
                $sender->id,
                FeedbackMessage::class,
                $feedback->id
            );
        }

        foreach ($recipients as $recipient) {
            $this->sendFeedbackMail(
                actorId: $sender->id,
                toEmail: (string) $recipient->email,
                subject: "[Feedback] Escalated: {$feedback->subject}",
                bodySummary: "feedback_message_id={$feedback->id}; from={$sender->email}; to_position={$nextPosition->name}; type=feedback_escalation",
                body: "Feedback cua {$sender->name} ({$sender->email}) da qua han xu ly va duoc chuyen len {$nextPosition->name}.\nTieu de: {$feedback->subject}\nNoi dung: {$feedback->message}\nVui long vao he thong de xu ly."
            );
        }
    }

    private function directSuperiorFeedbackPositions(User $sender): Collection
    {
        $senderLevel = (int) ($sender->employeeProfile?->position?->authority_level ?? 0);

        if ($senderLevel <= 0) {
            return collect();
        }

        return $this->nextSuperiorFeedbackPositions($sender, $senderLevel);
    }

    private function nextSuperiorFeedbackPositions(User $sender, int $baseLevel): Collection
    {
        if ($baseLevel <= 0) {
            return collect();
        }

        $positions = Position::query()
            ->where('is_active', true)
            ->where('authority_level', '>', $baseLevel)
            ->orderBy('authority_level')
            ->orderBy('name')
            ->get(['id', 'name', 'authority_level'])
            ->filter(fn (Position $position) => $this->positionHasFeedbackRecipient($position, $sender))
            ->values();

        $nextLevel = $positions->min('authority_level');

        if (!$nextLevel) {
            return collect();
        }

        return $positions
            ->filter(fn (Position $position) => (int) $position->authority_level === (int) $nextLevel)
            ->values();
    }

    private function positionHasFeedbackRecipient(Position $position, User $sender): bool
    {
        return $this->recipientsForPosition($position, $sender)->isNotEmpty();
    }

    private function canProcessFeedback(User $user): bool
    {
        return $user->hasAnyPositionCapability([
            PositionCapability::REPLY_FEEDBACK,
            PositionCapability::MANAGE_FEEDBACKS,
        ]);
    }

    private function sendFeedbackMail(
        int $actorId,
        string $toEmail,
        string $subject,
        string $bodySummary,
        string $body
    ): void {
        if (blank($toEmail)) {
            return;
        }

        try {
            Mail::raw($body, function ($message) use ($toEmail, $subject): void {
                $message->to($toEmail)->subject($subject);
            });

            EmailLog::query()->create([
                'sender_id' => $actorId,
                'receiver_email' => $toEmail,
                'subject' => $subject,
                'body_summary' => $bodySummary,
                'sent_at' => now(),
                'status' => 'success',
                'error_message' => null,
            ]);
        } catch (\Throwable $exception) {
            EmailLog::query()->create([
                'sender_id' => $actorId,
                'receiver_email' => $toEmail,
                'subject' => $subject,
                'body_summary' => $bodySummary,
                'sent_at' => now(),
                'status' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 1000),
            ]);
        }
    }
}
