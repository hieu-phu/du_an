<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Services\Auth\FirstLoginOtpService;
use App\Services\Auth\LoginNotificationService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function __construct(
        protected FirstLoginOtpService $firstLoginOtpService,
        protected LoginNotificationService $loginNotificationService
    ) {
    }

    public function redirectToGoogle()
    {
        if (!$this->hasGoogleCredentials()) {
            return redirect('/login')->with('error', 'Thiếu cấu hình Google OAuth. Vui lòng khai báo GOOGLE_CLIENT_ID và GOOGLE_CLIENT_SECRET.');
        }

        return $this->googleDriver()
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            if (!$this->hasGoogleCredentials()) {
                return redirect('/login')->with('error', 'Thiếu cấu hình Google OAuth. Vui lòng kiểm tra file .env.');
            }

            $googleUser = $this->googleDriver()->user();

            $socialAccount = SocialAccount::query()
                ->where('provider', 'google')
                ->where('provider_id', $googleUser->id)
                ->first();

            $user = $socialAccount?->user;

            if (!$user && $googleUser->email) {
                $user = User::query()->where('email', $googleUser->email)->first();
            }

            if (!$user) {
                return redirect('/login')->with('error', 'Tài khoản không có quyền truy cập.');
            }

            $user->update([
                'avatar' => $googleUser->avatar,
                'thumbnail' => $googleUser->avatar,
                'email_verified_at' => $user->email_verified_at ?? now('Asia/Ho_Chi_Minh'),
                'status' => $user->status === 'pending' ? 'active' : $user->status,
            ]);

            SocialAccount::query()->updateOrCreate(
                [
                    'provider' => 'google',
                    'provider_id' => $googleUser->id,
                ],
                [
                    'user_id' => $user->id,
                    'provider_email' => $googleUser->email,
                    'avatar' => $googleUser->avatar,
                ]
            );

            if ($this->firstLoginOtpService->requiresOtp($user)) {
                $this->firstLoginOtpService->sendOtp($user);
                $this->firstLoginOtpService->storePendingLogin(request(), $user, 'Google', $this->getRedirectUrl());

                return redirect()->route('login.otp.view');
            }

            Auth::login($user);
            request()->session()->regenerate();

            $this->loginNotificationService->handleSuccessfulLogin(
                $user,
                request(),
                'Google'
            );

            $user = $user->fresh();

            if (empty($user->phone)) {
                session(['url.intended' => $this->getRedirectUrl()]);

                return redirect()->route('phone.index');
            }

            return redirect()->intended($this->getRedirectUrl());
        } catch (\Exception $e) {
            logger()->error('Google OAuth Error: ' . $e->getMessage());

            return redirect('/login')->with('error', 'Không thể đăng nhập bằng Google. Vui lòng thử lại.');
        }
    }

    public function getRedirectUrl(): string
    {
        $host = request()->getHost();
        $mainDomain = env('APP_DOMAIN');

        if ($host === $mainDomain) {
            return '/dashboard';
        }

        $subdomain = str_replace('.' . $mainDomain, '', $host);

        $subdomainRoutes = [
            'ban-hang' => '/',
            'mua-hang' => '/',
            'kho' => '/',
            'thu-chi' => '/',
        ];

        return $subdomainRoutes[$subdomain] ?? '/document';
    }

    private function hasGoogleCredentials(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }

    private function googleDriver()
    {
        config(['services.google.redirect' => url('/login/google/callback')]);

        $driver = Socialite::driver('google');

        if (app()->environment('local')) {
            $driver->setHttpClient(new Client([
                'verify' => false,
                'timeout' => 15,
            ]));
        }

        return $driver;
    }

    private function generateUniqueUsername(string $email): string
    {
        $username = Str::before($email, '@');
        $originalUsername = $username;
        $counter = 1;

        while (User::query()->where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
