<?php

namespace Tests\Feature\Auth;

use App\Mail\FirstLoginOtpMail;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_first_login_requires_otp_verification(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login.otp.view', absolute: false));
        $this->assertDatabaseHas('login_otps', [
            'user_id' => $user->id,
        ]);
        Mail::assertQueued(FirstLoginOtpMail::class);
    }

    public function test_users_can_complete_first_login_after_valid_otp(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $otp = LoginOtp::query()->where('user_id', $user->id)->firstOrFail();

        $response = $this->post('/login/verify-otp', [
            'otp' => $otp->otp,
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/document');
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_returning_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'last_login_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/document');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
