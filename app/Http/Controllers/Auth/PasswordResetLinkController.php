<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Email không tá»“n táº¡i trong há»‡ thá»‘ng.'
        ]);

        $existingOtp = PasswordResetOtp::where('email', $request->email)->first();
        if ($existingOtp && $existingOtp->updated_at->addMinute()->isFuture()) {
            $secondsLeft = now()->diffInSeconds($existingOtp->updated_at->addMinute());
            return back()->withErrors([
                'email' => "Vui lòng chờ {$secondsLeft} giây trưá»›c khi yêu cáº§u mã má»›i."
            ]);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        PasswordResetOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15),
            ]
        );

        Mail::to($request->email)->queue(new PasswordResetOtpMail($otp));

        return redirect()->route('password.otp.view', ['email' => $request->email]);
    }

    /**
     * Display the OTP verification view.
     */
    public function otpView(Request $request): Response
    {
        return Inertia::render('Auth/VerifyOtp', [
            'email' => $request->email,
            'status' => session('status'),
            'context' => 'password-reset',
            'title' => 'Xác nhận ma OTP',
            'description' => 'Chung toi da gui ma xác nhận 6 so den email',
            'submitRoute' => route('password.otp.verify'),
            'resendRoute' => route('password.email'),
            'changeRoute' => route('password.request'),
            'changeLabel' => 'Thay doi email khac',
        ]);
    }

    /**
     * Verify the OTP.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Mã xác nháº­n không chính xác hoáº·c Ä‘ã háº¿t háº¡n.']);
        }

        // Generate a temporary token to pass to the password reset page
        $token = bin2hex(random_bytes(32));
        
        // We can reuse Laravel's password_reset_tokens table for compatibility or just pass email + token
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }
}

