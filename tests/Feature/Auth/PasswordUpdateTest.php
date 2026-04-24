<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordChangeOtpMail;
use App\Models\PasswordChangeOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_change_requests_an_otp_before_updating_password(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $response = $this
            ->actingAs($user)
            ->from('/my-profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/my-profile');

        Mail::assertSent(PasswordChangeOtpMail::class);
        $this->assertDatabaseHas('password_change_otps', [
            'user_id' => $user->id,
        ]);
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_password_is_updated_after_valid_otp_verification(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this
            ->actingAs($user)
            ->from('/my-profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/my-profile');

        $otp = PasswordChangeOtp::query()->where('user_id', $user->id)->value('otp');

        $response = $this
            ->actingAs($user)
            ->from('/my-profile')
            ->post('/password/verify-otp', [
                'otp' => $otp,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/my-profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
        $this->assertDatabaseMissing('password_change_otps', [
            'user_id' => $user->id,
        ]);
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/my-profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect('/my-profile');
    }

    public function test_password_update_validation_uses_vietnamese_attribute_names(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/my-profile')
            ->put('/password', []);

        $response
            ->assertSessionHasErrors(['current_password', 'password'])
            ->assertRedirect('/my-profile');

        $errors = session('errors')->getBag('default');

        $this->assertStringContainsString('Mật khẩu hiện tại', $errors->first('current_password'));
        $this->assertStringContainsString('Mật khẩu mới', $errors->first('password'));
    }

    public function test_password_is_not_updated_when_otp_is_invalid(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this
            ->actingAs($user)
            ->from('/my-profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response = $this
            ->actingAs($user)
            ->from('/my-profile')
            ->post('/password/verify-otp', [
                'otp' => '000000',
            ]);

        $response
            ->assertSessionHasErrors('otp')
            ->assertRedirect('/my-profile');

        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_pending_password_change_can_be_cancelled(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this
            ->actingAs($user)
            ->from('/my-profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/my-profile');

        $this->assertDatabaseHas('password_change_otps', [
            'user_id' => $user->id,
        ]);
        $this->assertTrue(session()->has(\App\Http\Controllers\Auth\PasswordController::SESSION_KEY));

        $this
            ->actingAs($user)
            ->from('/my-profile')
            ->delete('/password/change-otp')
            ->assertSessionHasNoErrors()
            ->assertRedirect('/my-profile');

        $this->assertDatabaseMissing('password_change_otps', [
            'user_id' => $user->id,
        ]);
        $this->assertFalse(session()->has(\App\Http\Controllers\Auth\PasswordController::SESSION_KEY));
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }
}
