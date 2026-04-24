<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginOtp;
use App\Models\User;
use App\Services\AuditTrailService;
use App\Services\Auth\FirstLoginOtpService;
use App\Services\Auth\LoginNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected AuditTrailService $auditTrailService,
        protected FirstLoginOtpService $firstLoginOtpService,
        protected LoginNotificationService $loginNotificationService
    ) {
    }

    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $user = $request->authenticate();

        if ($this->firstLoginOtpService->requiresOtp($user)) {
            $this->firstLoginOtpService->sendOtp($user);
            $this->firstLoginOtpService->storePendingLogin($request, $user, 'Email va mật khẩu', $request->getRedirectUrl());

            return redirect()->route('login.otp.view');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $this->loginNotificationService->handleSuccessfulLogin(
            $user,
            $request,
            'Email va mật khẩu'
        );

        $request->session()->save();

        return redirect()->intended($request->getRedirectUrl());
    }

    public function showFirstLoginOtp(Request $request): Response|RedirectResponse
    {
        $pendingLogin = $request->session()->get(FirstLoginOtpService::SESSION_KEY);

        if (!$pendingLogin || empty($pendingLogin['user_id'])) {
            return redirect()->route('login');
        }

        $user = User::query()->find($pendingLogin['user_id']);

        if (!$user) {
            $request->session()->forget(FirstLoginOtpService::SESSION_KEY);

            return redirect()->route('login');
        }

        return Inertia::render('Auth/VerifyOtp', [
            'email' => $user->email,
            'status' => session('status'),
            'context' => 'first-login',
            'title' => 'Xác nhận OTP dang nhap',
            'description' => 'Chung toi da gui ma OTP 6 so den email',
            'submitRoute' => route('login.otp.verify'),
            'resendRoute' => route('login.otp.resend'),
            'changeRoute' => route('login'),
            'changeLabel' => 'Quay lai dang nhap',
        ]);
    }

    public function verifyFirstLoginOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $pendingLogin = $request->session()->get(FirstLoginOtpService::SESSION_KEY);

        if (!$pendingLogin || empty($pendingLogin['user_id'])) {
            return redirect()->route('login')->with('error', 'Phien xac thuc da het han. Vui lòng dang nhap lai.');
        }

        $user = User::query()->find($pendingLogin['user_id']);

        if (!$user) {
            $request->session()->forget(FirstLoginOtpService::SESSION_KEY);

            return redirect()->route('login')->with('error', 'Tài khoản không ton tai.');
        }

        $otpRecord = LoginOtp::query()
            ->where('user_id', $user->id)
            ->where('otp', $request->string('otp')->toString())
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (!$otpRecord) {
            return back()->withErrors([
                'otp' => 'Ma OTP không chinh xac hoac da het han.',
            ]);
        }

        $otpRecord->delete();

        Auth::login($user, (bool) ($pendingLogin['remember'] ?? false));
        $request->session()->regenerate();

        $this->loginNotificationService->handleSuccessfulLogin(
            $user,
            $request,
            (string) ($pendingLogin['login_method'] ?? 'Email va mật khẩu')
        );

        $request->session()->forget(FirstLoginOtpService::SESSION_KEY);
        $request->session()->save();

        $redirectUrl = (string) ($pendingLogin['redirect_url'] ?? '/dashboard');

        if (($pendingLogin['login_method'] ?? null) === 'Google' && blank($user->phone)) {
            session(['url.intended' => $redirectUrl]);

            return redirect()->route('phone.index');
        }

        return redirect()->to($redirectUrl);
    }

    public function resendFirstLoginOtp(Request $request): RedirectResponse
    {
        $pendingLogin = $request->session()->get(FirstLoginOtpService::SESSION_KEY);

        if (!$pendingLogin || empty($pendingLogin['user_id'])) {
            return redirect()->route('login')->with('error', 'Phien xac thuc da het han. Vui lòng dang nhap lai.');
        }

        $user = User::query()->find($pendingLogin['user_id']);

        if (!$user) {
            $request->session()->forget(FirstLoginOtpService::SESSION_KEY);

            return redirect()->route('login')->with('error', 'Tài khoản không ton tai.');
        }

        $otpRecord = LoginOtp::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->first();

        if ($otpRecord && $otpRecord->updated_at->addMinute()->isFuture()) {
            $secondsLeft = now()->diffInSeconds($otpRecord->updated_at->addMinute());

            return back()->withErrors([
                'otp' => "Vui lòng cho {$secondsLeft} giay truoc khi gui lai ma OTP.",
            ]);
        }

        $this->firstLoginOtpService->sendOtp($user);

        return back()->with('status', 'Da gui lai ma OTP moi.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            $loggedOutAt = now('Asia/Ho_Chi_Minh');
            $userAgent = (string) ($request->userAgent() ?: 'Không xác định');

            \App\Models\LoginHistory::query()
                ->where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first()
                ?->update([
                    'logout_at' => $loggedOutAt,
                ]);

            $this->auditTrailService->log([
                'user_id' => $user->id,
                'module' => 'auth',
                'action' => 'logout',
                'description' => 'Dang xuat khoi hệ thống',
                'reference_table' => 'users',
                'reference_id' => $user->id,
                'ip_address' => $request->ip(),
                'device' => $this->auditTrailService->detectDevice($userAgent),
                'user_agent' => $userAgent,
                'occurred_at' => $loggedOutAt,
            ], $request);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}


