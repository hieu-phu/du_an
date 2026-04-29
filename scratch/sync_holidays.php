<?php

use App\Models\Holiday;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$recurringPatterns = [
    'Tết Dương lịch',
    'Ngày Giải phóng Miền Nam',
    'Ngày Quốc tế Lao động',
    'Quốc khánh',
];

$updatedCount = 0;

Holiday::all()->each(function (Holiday $holiday) use ($recurringPatterns, &$updatedCount) {
    $originalName = $holiday->holiday_name;
    
    // 1. Detect if it should be recurring (Solar holidays only)
    $shouldBeRecurring = false;
    foreach ($recurringPatterns as $pattern) {
        if (stripos($originalName, $pattern) !== false) {
            $shouldBeRecurring = true;
            break;
        }
    }
    
    // 2. Detect Lunar (to NOT set recurring if it's Lunar, because Solar date changes)
    // Wait, the user might want it to "recur" in the UI, but our logic uses Solar dates.
    // If we mark "Tết Nguyên Đán 2026" as recurring, next year it will use the same Solar date.
    // So we ONLY set recurring for Solar holidays.
    $isLunar = preg_match('/Tết (Nguyên Đán|Âm)|Giỗ tổ Hùng Vương/i', $originalName);
    
    if ($isLunar) {
        $shouldBeRecurring = false;
    }

    // 3. Strip year from name (since we did it in display, let's do it in DB too for consistency)
    $newName = $originalName;
    if (!$isLunar) {
        $newName = preg_replace('/\s+\d{4}$/', '', $originalName);
    }

    if ($shouldBeRecurring !== (bool)$holiday->is_recurring || $newName !== $originalName) {
        $holiday->update([
            'is_recurring' => $shouldBeRecurring,
            'holiday_name' => $newName
        ]);
        $updatedCount++;
    }
});

echo "Updated $updatedCount holidays.";
