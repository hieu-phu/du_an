<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\FeedbackEscalation;
use App\Models\FeedbackMessage;
use App\Models\FeedbackReply;
use App\Models\Position;
use App\Models\User;
use App\Services\FeedbackEscalationService;
use App\Services\NotificationService;
use App\Support\AccessMatrix;
use App\Support\PositionCapability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected FeedbackEscalationService $feedbackEscalationService
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $status = $request->input('status');
        $processingState = $request->input('processing_state');

        $sentQuery = FeedbackMessage::query()
            ->with(['receiver:id,name,email', 'receiverPosition:id,name', 'replier:id,name,email'])
            ->where('sender_id', $user->id)
            ->with(['replies.replier:id,name,email'])
            ->with(['escalations.fromPosition:id,name,authority_level', 'escalations.toPosition:id,name,authority_level'])
            ->when($status, fn (Builder $q) => $q->where('status', $status));
        $this->applyProcessingStateFilter($sentQuery, $processingState);
        $sent = $sentQuery
            ->latest()
            ->limit(100)
            ->get();

        $inbox = [];
        $inboxQuery = null;
        if ($this->canViewFeedbackInbox($user)) {
            $inboxQuery = FeedbackMessage::query()
                ->with(['sender:id,name,email', 'receiverPosition:id,name', 'replier:id,name,email'])
                ->with(['replies.replier:id,name,email'])
                ->with(['escalations.fromPosition:id,name,authority_level', 'escalations.toPosition:id,name,authority_level'])
                ->where(function (Builder $q) use ($user) {
                    $groups = $this->receiverGroupsForUser($user);
                    $q->where('receiver_id', $user->id);

                    $positionId = $user->employeeProfile?->position_id;
                    if ($positionId) {
                        $q->orWhere('receiver_position_id', $positionId);
                    }

                    if (!empty($groups)) {
                        $q->orWhereIn('receiver_group', $groups);
                    }
                })
                ->when($status, fn (Builder $q) => $q->where('status', $status));

            $this->applyProcessingStateFilter($inboxQuery, $processingState);
            $inbox = $inboxQuery
                ->latest()
                ->limit(100)
                ->get();
        }

        $sentMapped = $sent
            ->map(fn (FeedbackMessage $item) => $this->mapFeedback($item))
            ->values()
            ->all();

        $inboxMapped = collect($inbox)
            ->map(fn (FeedbackMessage $item) => $this->mapFeedback($item))
            ->values()
            ->all();

        $mailLogs = [];
        if ($user->hasPositionCapability(PositionCapability::MANAGE_FEEDBACKS)) {
            $mailLogs = EmailLog::query()
                ->with('sender:id,name,email')
                ->where('subject', 'like', '[Feedback]%')
                ->latest()
                ->limit(100)
                ->get()
                ->map(fn (EmailLog $log) => [
                    'id' => $log->id,
                    'receiver_email' => $log->receiver_email,
                    'subject' => $log->subject,
                    'status' => $log->status,
                    'error_message' => $log->error_message,
                    'sent_at' => optional($log->sent_at)->toDateTimeString(),
                    'sender' => $log->sender ? [
                        'id' => $log->sender->id,
                        'name' => $log->sender->name,
                        'email' => $log->sender->email,
                    ] : null,
                ])
                ->values()
                ->all();
        }

        return Inertia::render('Feedback/Index', [
            'sent' => $sentMapped,
            'inbox' => $inboxMapped,
            'mailLogs' => $mailLogs,
            'filters' => [
                'status' => $status,
                'processing_state' => $processingState,
            ],
            'canReply' => $this->canViewFeedbackInbox($user),
            'canSubmitReply' => $user->hasAnyPositionCapability([
                PositionCapability::REPLY_FEEDBACK,
                PositionCapability::MANAGE_FEEDBACKS,
            ]),
            'receiverOptions' => $this->feedbackEscalationService->receiverOptions($user),
            'statusOptions' => [
                ['value' => 'sent', 'label' => 'Da gui'],
                ['value' => 'read', 'label' => 'Da doc'],
                ['value' => 'archived', 'label' => 'Da luu'],
            ],
            'processingOptions' => [
                ['value' => 'unprocessed', 'label' => 'Chua xu ly'],
                ['value' => 'processed', 'label' => 'Da xu ly'],
            ],
            'summary' => [
                'sent_total' => $sent->count(),
                'sent_unprocessed' => $sent->where('is_replied', false)->count(),
                'sent_processed' => $sent->where('is_replied', true)->count(),
                'inbox_total' => collect($inbox)->count(),
                'inbox_unprocessed' => collect($inbox)->where('is_replied', false)->count(),
                'inbox_processed' => collect($inbox)->where('is_replied', true)->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'receiver_position_id' => ['required', 'integer', 'exists:positions,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $receiverPosition = Position::query()
            ->whereKey((int) $validated['receiver_position_id'])
            ->where('is_active', true)
            ->firstOrFail();

        if (!$this->feedbackEscalationService->canSendToPosition($user, $receiverPosition)) {
            throw ValidationException::withMessages([
                'receiver_position_id' => 'Chi duoc gui phan hoi den cap bac cao hon va dang co nguoi xu ly.',
            ]);
        }

        $feedback = FeedbackMessage::query()->create([
            'sender_id' => $user->id,
            'receiver_group' => 'specific_user',
            'receiver_position_id' => $receiverPosition->id,
            'receiver_id' => null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'sent',
        ]);

        $recipients = $this->feedbackEscalationService->recipientsForPosition($receiverPosition, $user);

        $recipientIds = $recipients->pluck('id')->all();

        if (!empty($recipientIds)) {
            $this->notificationService->createForUsers(
                $recipientIds,
                'Phan hoi noi bo moi',
                "{$user->name} vua gui phan hoi: {$feedback->subject}",
                [
                    'feedback_message_id' => $feedback->id,
                    'action_url' => '/feedbacks',
                ],
                '/feedbacks',
                null,
                'feedback',
                $user->id,
                FeedbackMessage::class,
                $feedback->id
            );
        }

        foreach ($recipients as $recipient) {
            $this->sendFeedbackMail(
                actorId: $user->id,
                toEmail: (string) $recipient->email,
                subject: "[Feedback] Phan hoi moi: {$feedback->subject}",
                bodySummary: "feedback_message_id={$feedback->id}; from={$user->email}; to_position={$receiverPosition->name}; type=new_feedback",
                body: "Ban co phan hoi moi tu {$user->name} ({$user->email}).\nTieu de: {$feedback->subject}\nNoi dung: {$feedback->message}\nVui long vao he thong de xu ly."
            );
        }

        return redirect()->back()->with('success', 'Da gui phan hoi thanh cong.');
    }

    public function reply(Request $request, FeedbackMessage $feedbackMessage)
    {
        $user = $request->user();
        abort_unless($this->canReplyFeedback($user, $feedbackMessage), 403);

        $validated = $request->validate([
            'reply_message' => ['required', 'string', 'max:5000'],
            'status' => ['nullable', 'in:sent,read,archived'],
        ]);

        FeedbackReply::query()->create([
            'feedback_message_id' => $feedbackMessage->id,
            'replied_by' => $user->id,
            'message' => $validated['reply_message'],
        ]);

        $isSenderReply = $feedbackMessage->sender_id === $user->id;

        $feedbackMessage->update(
            $isSenderReply
                ? [
                    'reply_message' => $validated['reply_message'],
                    'replied_by' => $user->id,
                    'replied_at' => now(),
                    'is_replied' => false,
                    'status' => $validated['status'] ?? 'sent',
                    'read_at' => null,
                ]
                : [
                    'reply_message' => $validated['reply_message'],
                    'replied_by' => $user->id,
                    'replied_at' => now(),
                    'is_replied' => true,
                    'status' => $validated['status'] ?? 'read',
                    'read_at' => $feedbackMessage->read_at ?: now(),
                ]
        );

        if ($isSenderReply) {
            $this->notifyFeedbackRecipients($feedbackMessage, $user, $validated['reply_message']);
        } else {
            $this->notifyFeedbackSender($feedbackMessage, $user, $validated['reply_message']);
        }

        return redirect()->back()->with('success', 'Da tra loi phan hoi.');
    }

    public function markRead(Request $request, FeedbackMessage $feedbackMessage)
    {
        $user = $request->user();
        abort_unless($this->canReadFeedback($user, $feedbackMessage), 403);

        $feedbackMessage->update([
            'status' => 'read',
            'read_at' => $feedbackMessage->read_at ?: now(),
        ]);

        return redirect()->back();
    }

    private function canReadFeedback(User $user, FeedbackMessage $feedbackMessage): bool
    {
        if ($feedbackMessage->sender_id === $user->id) {
            return true;
        }

        if ($feedbackMessage->receiver_id && $feedbackMessage->receiver_id === $user->id) {
            return true;
        }

        if (
            $feedbackMessage->receiver_position_id
            && $this->feedbackEscalationService->canReceiveFeedbackForPosition($user, (int) $feedbackMessage->receiver_position_id)
        ) {
            return true;
        }

        if (AccessMatrix::canReceiveFeedbackGroup($user, (string) $feedbackMessage->receiver_group)) {
            return true;
        }

        return false;
    }

    private function canReplyFeedback(User $user, FeedbackMessage $feedbackMessage): bool
    {
        if ($feedbackMessage->sender_id === $user->id) {
            return true;
        }

        if ($feedbackMessage->receiver_id) {
            return $feedbackMessage->receiver_id === $user->id;
        }

        if ($feedbackMessage->receiver_position_id) {
            return $this->feedbackEscalationService->canReplyFeedbackForPosition($user, (int) $feedbackMessage->receiver_position_id);
        }

        if (AccessMatrix::canReceiveFeedbackGroup($user, (string) $feedbackMessage->receiver_group)) {
            return true;
        }

        return false;
    }

    private function receiverGroupsForUser(User $user): array
    {
        $groups = [];

        if (AccessMatrix::canReceiveFeedbackGroup($user, 'admin')) {
            $groups[] = 'admin';
        }

        if (AccessMatrix::canReceiveFeedbackGroup($user, 'hr')) {
            $groups[] = 'hr';
        }

        return $groups;
    }

    private function canViewFeedbackInbox(User $user): bool
    {
        return $user->hasAnyPositionCapability([
            PositionCapability::VIEW_FEEDBACKS,
            PositionCapability::REPLY_FEEDBACK,
            PositionCapability::MANAGE_FEEDBACKS,
        ]);
    }

    private function applyProcessingStateFilter(Builder $query, ?string $processingState): void
    {
        if ($processingState === 'processed') {
            $query->where('is_replied', true);
        }

        if ($processingState === 'unprocessed') {
            $query->where('is_replied', false);
        }
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

    private function mapFeedback(FeedbackMessage $item): array
    {
        return [
            'id' => $item->id,
            'subject' => $item->subject,
            'message' => $item->message,
            'receiver_group' => $item->receiver_group,
            'receiver_group_label' => $item->receiverPosition?->name ?: match ($item->receiver_group) {
                'admin' => 'Admin',
                'hr' => 'HR',
                'specific_user' => 'Nguoi cu the',
                default => '-',
            },
            'status' => $item->status,
            'status_label' => match ($item->status) {
                'sent' => 'Da gui',
                'read' => 'Da doc',
                'archived' => 'Da luu',
                default => '-',
            },
            'processing_state' => $item->is_replied ? 'processed' : 'unprocessed',
            'processing_state_label' => $item->is_replied ? 'Da xu ly' : 'Chua xu ly',
            'is_replied' => (bool) $item->is_replied,
            'escalation_count' => (int) ($item->escalation_count ?? 0),
            'last_escalated_at' => optional($item->last_escalated_at)->toDateTimeString(),
            'escalation_history' => $item->escalations->map(function (FeedbackEscalation $escalation): array {
                return [
                    'id' => $escalation->id,
                    'escalation_count' => (int) $escalation->escalation_count,
                    'escalated_at' => optional($escalation->escalated_at)->toDateTimeString(),
                    'reason' => $escalation->reason,
                    'from_position' => $escalation->fromPosition ? [
                        'id' => $escalation->fromPosition->id,
                        'name' => $escalation->fromPosition->name,
                        'authority_level' => $escalation->fromPosition->authority_level,
                    ] : null,
                    'to_position' => $escalation->toPosition ? [
                        'id' => $escalation->toPosition->id,
                        'name' => $escalation->toPosition->name,
                        'authority_level' => $escalation->toPosition->authority_level,
                    ] : null,
                ];
            })->values()->all(),
            'reply_message' => $item->reply_message,
            'reply_history' => $item->replies->map(function (FeedbackReply $reply): array {
                return [
                    'id' => $reply->id,
                    'message' => $reply->message,
                    'created_at' => optional($reply->created_at)->toDateTimeString(),
                    'replier' => $reply->replier ? [
                        'id' => $reply->replier->id,
                        'name' => $reply->replier->name,
                        'email' => $reply->replier->email,
                    ] : null,
                ];
            })->values()->all(),
            'sender' => $item->sender ? [
                'id' => $item->sender->id,
                'name' => $item->sender->name,
                'email' => $item->sender->email,
            ] : null,
            'replier' => $item->replier ? [
                'id' => $item->replier->id,
                'name' => $item->replier->name,
                'email' => $item->replier->email,
            ] : null,
            'created_at' => optional($item->created_at)->toDateTimeString(),
            'read_at' => optional($item->read_at)->toDateTimeString(),
            'replied_at' => optional($item->replied_at)->toDateTimeString(),
        ];
    }

    private function notifyFeedbackSender(FeedbackMessage $feedbackMessage, User $actor, string $replyMessage): void
    {
        $this->notificationService->create(
            $feedbackMessage->sender_id,
            'Phan hoi da duoc tra loi',
            "Yeu cau '{$feedbackMessage->subject}' da duoc {$actor->name} phan hoi.",
            [
                'feedback_message_id' => $feedbackMessage->id,
                'action_url' => '/feedbacks',
            ],
            '/feedbacks',
            null,
            'feedback',
            $actor->id,
            FeedbackMessage::class,
            $feedbackMessage->id
        );

        $sender = $feedbackMessage->sender()->first(['id', 'name', 'email']);
        if ($sender && !blank($sender->email)) {
            $this->sendFeedbackMail(
                actorId: $actor->id,
                toEmail: (string) $sender->email,
                subject: "[Feedback] Yeu cau da duoc tra loi: {$feedbackMessage->subject}",
                bodySummary: "feedback_message_id={$feedbackMessage->id}; sender_id={$sender->id}; type=reply",
                body: "{$actor->name} da tra loi phan hoi cua ban.\nTieu de: {$feedbackMessage->subject}\nNoi dung tra loi: {$replyMessage}\nVui long vao he thong de xem chi tiet."
            );
        }
    }

    private function notifyFeedbackRecipients(FeedbackMessage $feedbackMessage, User $actor, string $replyMessage): void
    {
        $recipientIds = collect();
        $recipients = collect();

        if ($feedbackMessage->receiver_id) {
            $receiver = User::query()
                ->whereKey($feedbackMessage->receiver_id)
                ->whereKeyNot($actor->id)
                ->get(['id', 'name', 'email']);
            $recipients = $receiver;
            $recipientIds = $receiver->pluck('id');
        } elseif ($feedbackMessage->receiver_position_id) {
            $position = Position::query()->find($feedbackMessage->receiver_position_id);
            if ($position) {
                $recipients = $this->feedbackEscalationService->recipientsForPosition($position, $actor);
                $recipientIds = $recipients->pluck('id');
            }
        }

        if ($recipientIds->isNotEmpty()) {
            $this->notificationService->createForUsers(
                $recipientIds->all(),
                'Phan hoi noi bo co tin nhan moi',
                "{$actor->name} vua phan hoi them: {$feedbackMessage->subject}",
                [
                    'feedback_message_id' => $feedbackMessage->id,
                    'action_url' => '/feedbacks',
                ],
                '/feedbacks',
                null,
                'feedback',
                $actor->id,
                FeedbackMessage::class,
                $feedbackMessage->id
            );
        }

        foreach ($recipients as $recipient) {
            if (blank($recipient->email)) {
                continue;
            }

            $this->sendFeedbackMail(
                actorId: $actor->id,
                toEmail: (string) $recipient->email,
                subject: "[Feedback] Co tin nhan moi: {$feedbackMessage->subject}",
                bodySummary: "feedback_message_id={$feedbackMessage->id}; actor_id={$actor->id}; type=thread_reply",
                body: "{$actor->name} vua gui them mot tin nhan trong feedback.\nTieu de: {$feedbackMessage->subject}\nNoi dung: {$replyMessage}\nVui long vao he thong de xem va xu ly."
            );
        }
    }
}
