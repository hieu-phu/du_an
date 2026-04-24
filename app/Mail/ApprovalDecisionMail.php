<?php

namespace App\Mail;

use App\Models\ApprovalDecisionDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ApprovalDecisionDelivery $delivery,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->delivery->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approvals.decision_mail',
        );
    }
}
