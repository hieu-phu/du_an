<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuccessfulLoginMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $loginMethod,
        public string $ipAddress,
        public string $userAgent,
        public string $loggedInAt,
    ) {
    }

    public function envelope(): Envelope
    {
        $appName = config('app.name', 'HRM');

        return new Envelope(
            subject: '[' . $appName . '] Dang nhap thanh cong',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.successful-login',
        );
    }
}
