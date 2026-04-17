<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(2);

$codes = App\Support\PositionCapability::all();
$dbCodes = App\Models\PositionCapability::query()
    ->where('is_active', true)
    ->pluck('code')
    ->all();
$availableCapabilities = array_values(array_unique(array_merge($codes, $dbCodes)));

$filtered = array_filter(
    $availableCapabilities,
    fn ($cap) => $user->hasPositionCapability($cap)
);
$filled = array_fill_keys($filtered, true);

echo "filtered: \n";
var_dump($filtered);

echo "\nfilled: \n";
var_dump($filled);

echo "\nhasPositionCapability('approve_requests'): \n";
var_dump($user->hasPositionCapability('approve_requests'));

$posCaps = array_fill_keys($availableCapabilities, false) + $filled;
echo "\nposCaps['approve_requests']: \n";
var_dump($posCaps['approve_requests']);
