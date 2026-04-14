<?php

namespace App\Services\Auth;

use App\Mail\SuccessfulLoginMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LoginNotificationService
{
    public function handleSuccessfulLogin(User $user, Request $request, string $loginMethod): void
    {
        $ipAddress = $request->ip() ?? 'Khong xac dinh';
        $userAgent = (string) ($request->userAgent() ?: 'Khong xac dinh');
        $loggedInAt = now('Asia/Ho_Chi_Minh');

        $user->forceFill([
            'last_login_at' => $loggedInAt,
            'last_login_ip' => $ipAddress,
        ])->save();

        if (blank($user->email)) {
            return;
        }

        Mail::to($user->email)->queue(new SuccessfulLoginMail(
            user: $user->fresh(),
            loginMethod: $loginMethod,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
            loggedInAt: $loggedInAt->format('d/m/Y H:i:s'),
        ));
    }
}
