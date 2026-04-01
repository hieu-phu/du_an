<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect user to Google OAuth page
     */
    public function redirectToGoogle()
    { // Tạo redirect URL động theo domain hiện tại
        $redirectUrl = url('/login/google/callback');
        // Ghi đè config tạm thời cho Socialite
        config(['services.google.redirect' => $redirectUrl]);
        // dd(config('services.google.redirect'));
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google OAuth
     */
    public function handleGoogleCallback()
    {
        try {
            $redirectUrl = url('/login/google/callback');
            // Ghi đè config tạm thời cho Socialite
            config(['services.google.redirect' => $redirectUrl]);
            // Get user info from Google
            $googleUser = Socialite::driver('google')->user();

            // Find or create user
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Update existing user with Google info
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);
            } else {
                // Generate unique username from email
                $username = explode('@', $googleUser->email)[0];
                $originalUsername = $username;
                $counter = 1;

                // Ensure username is unique
                while (User::where('username', $username)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }

                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'username' => $username,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(Str::random(24)), // Random password
                    'email_verified_at' => now('Asia/Ho_Chi_Minh'), // Auto verify email for Google users
                    'status' => 'active', // Auto active for Google users
                ]);

                $user->assignRole('CompanyManager');
            }

            // Login user
            Auth::login($user, true);
            $user = $user->fresh();
            if (empty($user->phone)) {
                session(['url.intended' => $this->getRedirectUrl()]);
                return redirect()->route('phone.index');
            }
            // dd($request->getRedirectUrl());

            // Redirect to dashboard or intended page
            return redirect()->intended($this->getRedirectUrl());
        } catch (\Exception $e) {
            // Log error and redirect back with error message
            logger()->error('Google OAuth Error: ' . $e->getMessage());

            return redirect('/login')->with('error', 'Không thể đăng nhập bằng Google. Vui lòng thử lại.');
        }
    }
    public function getRedirectUrl(): string
    {
        $host = request()->getHost();
        $mainDomain = env('APP_DOMAIN');
        // Nếu là domain chính
        if ($host === $mainDomain) {
            return '/dashboard';
        }

        // Lấy subdomain
        $subdomain = str_replace('.' . $mainDomain, '', $host);

        // Định nghĩa redirect URL cho từng subdomain
        $subdomainRoutes = [
            'ban-hang' => '/',
            'mua-hang' => '/',
            'kho' => '/',
            'thu-chi' => '/',
        ];

        return $subdomainRoutes[$subdomain] ?? '/document';
    }
}
