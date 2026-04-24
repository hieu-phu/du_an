<?php

namespace App\Jobs;

use App\Enums\ApprovalDecisionDeliveryStatus;
use App\Mail\ApprovalDecisionMail;
use App\Models\ApprovalDecisionDelivery;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendApprovalDecisionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $deliveryId,
    ) {
        $this->afterCommit();
        $this->onQueue((string) config('approval_notifications.mail.queue', 'mail'));
    }

    public function handle(): void
    {
        $delivery = ApprovalDecisionDelivery::query()->find($this->deliveryId);

        if (!$delivery) {
            return;
        }

        if ($delivery->status === ApprovalDecisionDeliveryStatus::SENT) {
            return;
        }

        if (!filled($delivery->recipient_email)) {
            $delivery->update([
                'status' => ApprovalDecisionDeliveryStatus::SKIPPED,
                'failed_at' => now(),
                'last_error' => 'Missing recipient email.',
            ]);

            return;
        }

        $delivery->update([
            'attempt_count' => (int) $delivery->attempt_count + 1,
            'last_attempt_at' => now(),
            'status' => ApprovalDecisionDeliveryStatus::QUEUED,
            'last_error' => null,
        ]);

        Mail::to($delivery->recipient_email)->send(new ApprovalDecisionMail($delivery->fresh()));

        $delivery->refresh();
        $delivery->update([
            'status' => ApprovalDecisionDeliveryStatus::SENT,
            'sent_at' => now(),
            'failed_at' => null,
            'last_error' => null,
        ]);

        EmailLog::query()->create([
            'sender_id' => $delivery->triggered_by,
            'receiver_email' => $delivery->recipient_email,
            'subject' => $delivery->subject,
            'body_summary' => data_get($delivery->payload, 'item_label'),
            'sent_at' => now(),
            'status' => 'success',
            'error_message' => null,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $delivery = ApprovalDecisionDelivery::query()->find($this->deliveryId);

        if ($delivery) {
            $delivery->update([
                'status' => ApprovalDecisionDeliveryStatus::FAILED,
                'failed_at' => now(),
                'last_error' => mb_substr($exception->getMessage(), 0, 5000),
            ]);

            EmailLog::query()->create([
                'sender_id' => $delivery->triggered_by,
                'receiver_email' => $delivery->recipient_email,
                'subject' => $delivery->subject,
                'body_summary' => data_get($delivery->payload, 'item_label'),
                'sent_at' => now(),
                'status' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 5000),
            ]);
        }
    }
}
