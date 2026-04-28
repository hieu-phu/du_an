<?php

use App\Models\User;
use App\Services\LeaveManagementService;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(LeaveManagementService::class);
$admin = User::whereHas('authorityLevel', function($q) {
    $q->where('level', 10); // Assuming 10 is Admin
})->first() ?: User::first();

if ($admin) {
    Auth::login($admin);
    $count = $service->grantBulk($admin, ['year' => 2026]);
    echo "Successfully granted $count leave balance rows for 2026.";
} else {
    echo "No admin user found to perform bulk grant.";
}
