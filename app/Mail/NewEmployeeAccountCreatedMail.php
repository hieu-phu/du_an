<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEmployeeAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $admin,
        public User $actor,
        public User $createdUser,
    ) {
    }

    public function envelope(): Envelope
    {
        $appName = config('app.name', 'HRM');

        return new Envelope(
            subject: '[' . $appName . '] Tài khoản nhân viên mới vừa được tạo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hr.new-employee-account-created',
        );
    }
}
