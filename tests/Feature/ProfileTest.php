<?php

namespace Tests\Feature;

use App\Models\EmployeeProfile;
use App\Models\Position;
use App\Models\User;
use App\Support\PositionCapability as Capability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        $user = User::factory()->create();

        $position = Position::query()->create([
            'name' => 'Profile Test Position ' . $user->id,
            'description' => 'Generated for profile test',
            'is_active' => true,
            'authority_level' => 1,
            'capabilities' => [
                Capability::VIEW_OWN_PROFILE,
                Capability::UPDATE_OWN_PROFILE,
            ],
        ]);
        $position->syncCapabilityCodes([
            Capability::VIEW_OWN_PROFILE,
            Capability::UPDATE_OWN_PROFILE,
        ]);

        EmployeeProfile::query()->create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-' . str_pad((string) $user->id, 3, '0', STR_PAD_LEFT),
            'position_id' => $position->id,
            'hire_date' => now()->toDateString(),
            'employment_status' => 'active',
            'employment_type' => 'official',
            'base_salary' => 10000000,
        ]);

        return $user;
    }

    public function test_my_profile_page_is_displayed(): void
    {
        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->get('/my-profile');

        $response->assertOk();
    }

    public function test_my_profile_information_can_be_updated(): void
    {
        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->put('/my-profile', [
                'phone' => '0912345678',
                'address_line' => '123 Test Street',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();
        $profile = $user->employeeProfile()->first();

        $this->assertSame('0912345678', $user->phone);
        $this->assertSame('123 Test Street', $profile?->address_line);
    }

    public function test_profile_update_validates_province_id(): void
    {
        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->from('/my-profile')
            ->put('/my-profile', [
                'province_id' => 999999999,
            ]);

        $response
            ->assertSessionHasErrors(['province_id'])
            ->assertRedirect('/my-profile');
    }
}
