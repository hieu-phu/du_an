<?php

namespace App\Services\Auth;

use App\Mail\FirstLoginOtpMail;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FirstLoginOtpService
{
    public const SESSION_KEY = 'auth.first_login';

    public function requiresOtp(User $user): bool
    {
        return $user->last_login_at === null;
    }

    public function sendOtp(User $user): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        LoginOtp::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15),
            ]
        );

        Mail::to($user->email)->queue(new FirstLoginOtpMail($otp));
    }

    public function storePendingLogin(Request $request, User $user, string $loginMethod, ?string $redirectUrl = null): void
    {
        $request->session()->put(self::SESSION_KEY, [
            'user_id' => $user->id,
            'remember' => $request->boolean('remember'),
            'login_method' => $loginMethod,
            'redirect_url' => $redirectUrl ?? '/dashboard',
        ]);
        $request->session()->save();
    }
}
