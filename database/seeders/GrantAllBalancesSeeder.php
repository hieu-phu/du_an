<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\LeaveManagementService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class GrantAllBalancesSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(LeaveManagementService::class);
        $admin = User::whereHas('employeeProfile.position', function($q) {
            $q->where('authority_level', '>=', 10);
        })->first() ?: User::first();

        if ($admin) {
            Auth::login($admin);
            $count = $service->grantBulk($admin, ['year' => 2026]);
            $this->command->info("Successfully granted $count leave balance rows for 2026.");
        } else {
            $this->command->error("No admin user found to perform bulk grant.");
        }
    }
}
