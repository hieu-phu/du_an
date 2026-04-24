<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangeOtpMail;
use App\Models\PasswordChangeOtp;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public const SESSION_KEY = 'auth.password_change_otp';

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], attributes: $this->validationAttributes());

        $user = $request->user();

        if ($secondsLeft = $this->otpCooldownSeconds($user->id)) {
            return back()->withErrors([
                'otp' => "Vui lòng chờ {$secondsLeft} giây trước khi yêu cầu mã OTP mới.",
            ]);
        }

        $this->sendOtp($user->id, $user->email);

        $request->session()->put(self::SESSION_KEY, [
            'user_id' => $user->id,
            'password_hash' => Hash::make($validated['password']),
            'expires_at' => now()->addMinutes(15)->toIso8601String(),
        ]);
        $request->session()->save();

        return back();
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], attributes: $this->validationAttributes());

        $user = $request->user();
        $pendingPasswordChange = $request->session()->get(self::SESSION_KEY);

        if (!$this->hasValidPendingPasswordChange($pendingPasswordChange, $user->id)) {
            $request->session()->forget(self::SESSION_KEY);

            return back()->withErrors([
                'otp' => 'Phiên xác thực đổi mật khẩu đã hết hạn. Vui lòng thực hiện lại.',
            ]);
        }

        $otpRecord = PasswordChangeOtp::query()
            ->where('user_id', $user->id)
            ->where('otp', $request->string('otp')->toString())
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (!$otpRecord) {
            return back()->withErrors([
                'otp' => 'Mã OTP không chính xác hoặc đã hết hạn.',
            ]);
        }

        $user->update([
            'password' => $pendingPasswordChange['password_hash'],
        ]);

        $otpRecord->delete();
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->save();

        return back();
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $user = $request->user();
        $pendingPasswordChange = $request->session()->get(self::SESSION_KEY);

        if (!$this->hasValidPendingPasswordChange($pendingPasswordChange, $user->id)) {
            $request->session()->forget(self::SESSION_KEY);

            return back()->withErrors([
                'otp' => 'Phiên xác thực đổi mật khẩu đã hết hạn. Vui lòng nhập lại mật khẩu mới.',
            ]);
        }

        if ($secondsLeft = $this->otpCooldownSeconds($user->id)) {
            return back()->withErrors([
                'otp' => "Vui lòng chờ {$secondsLeft} giây trước khi gửi lại mã OTP.",
            ]);
        }

        $this->sendOtp($user->id, $user->email);

        return back();
    }

    public function cancelOtp(Request $request): RedirectResponse
    {
        $user = $request->user();

        PasswordChangeOtp::query()
            ->where('user_id', $user->id)
            ->delete();

        $request->session()->forget(self::SESSION_KEY);
        $request->session()->save();

        return back();
    }

    private function sendOtp(int $userId, string $email): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordChangeOtp::query()->updateOrCreate(
            ['user_id' => $userId],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15),
            ]
        );

        Mail::to($email)->send(new PasswordChangeOtpMail($otp));
    }

    private function otpCooldownSeconds(int $userId): int
    {
        $otpRecord = PasswordChangeOtp::query()
            ->where('user_id', $userId)
            ->latest('updated_at')
            ->first();

        if (!$otpRecord || !$otpRecord->updated_at->addMinute()->isFuture()) {
            return 0;
        }

        return now()->diffInSeconds($otpRecord->updated_at->addMinute());
    }

    private function hasValidPendingPasswordChange(mixed $pendingPasswordChange, int $userId): bool
    {
        if (!is_array($pendingPasswordChange)) {
            return false;
        }

        if ((int) ($pendingPasswordChange['user_id'] ?? 0) !== $userId) {
            return false;
        }

        if (blank($pendingPasswordChange['password_hash'] ?? null) || blank($pendingPasswordChange['expires_at'] ?? null)) {
            return false;
        }

        return Carbon::parse($pendingPasswordChange['expires_at'])->isFuture();
    }

    private function validationAttributes(): array
    {
        return [
            'current_password' => 'Mật khẩu hiện tại',
            'password' => 'Mật khẩu mới',
            'password_confirmation' => 'Nhập lại mật khẩu mới',
            'otp' => 'Mã OTP',
        ];
    }
}
