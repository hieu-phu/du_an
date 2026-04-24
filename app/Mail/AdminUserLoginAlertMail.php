<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminUserLoginAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $admin,
        public User $loggedInUser,
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
            subject: '[' . $appName . '] Canh bao dang nhap tài khoản',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.admin-user-login-alert',
        );
    }
}

