<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertRedirect(route('password.otp.view', ['email' => $user->email], false));
        $this->assertDatabaseHas('password_reset_otps', [
            'email' => $user->email,
        ]);
        Mail::assertQueued(PasswordResetOtpMail::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $otp = PasswordResetOtp::query()->where('email', $user->email)->firstOrFail();

        $verifyResponse = $this->post('/verify-otp', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $location = (string) $verifyResponse->headers->get('Location');
        $this->assertStringContainsString('/reset-password/', $location);

        $response = $this->get($location);
        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $otp = PasswordResetOtp::query()->where('email', $user->email)->firstOrFail();

        $verifyResponse = $this->post('/verify-otp', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $location = (string) $verifyResponse->headers->get('Location');
        $token = basename((string) parse_url($location, PHP_URL_PATH));

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));
    }
}
