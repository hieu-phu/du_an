<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\AttendanceService;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:mark-absent {date?}', function (?string $date = null) {
    $markedCount = app(AttendanceService::class)->markAbsencesForDate(
        $date ? Carbon::parse($date, 'Asia/Ho_Chi_Minh') : null
    );

    $this->info("Marked {$markedCount} attendance record(s) as absent.");
})->purpose('Auto mark absent employees who did not check in for a workday');
