<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Position;
use App\Models\PositionCapability as PositionCapabilityModel;
use App\Services\AttendanceService;
use App\Services\FeedbackEscalationService;
use App\Support\PositionRoleResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:mark-absent {date?}', function (?string $date = null) {
    $markedCount = app(AttendanceService::class)->markAbsencesForDate(
        $date ? Carbon::parse($date, 'Asia/Ho_Chi_Minh') : null
    );

    $this->info("Marked {$markedCount} attendance record(s) as absent.");
})->purpose('Auto mark absent employees who did not check in for a workday');

Artisan::command('attendance:close-unexplained-absences {--days=} {--as-of=}', function () {
    $days = max(1, (int) ($this->option('days') ?: config('attendance.auto_close_after_days', 2)));
    $asOf = $this->option('as-of')
        ? Carbon::parse((string) $this->option('as-of'), 'Asia/Ho_Chi_Minh')
        : null;
    $closedCount = app(AttendanceService::class)->closeUnexplainedAbsences($days, $asOf);

    $this->info("Closed {$closedCount} unexplained absence record(s) after {$days} day(s).");
})->purpose('Auto reject unexplained absences that have no active attendance request after a timeout');

Artisan::command('attendance:sync-missing-records {--from=} {--to=} {--auto-close}', function () {
    $from = $this->option('from')
        ? Carbon::parse((string) $this->option('from'), 'Asia/Ho_Chi_Minh')
        : null;
    $to = $this->option('to')
        ? Carbon::parse((string) $this->option('to'), 'Asia/Ho_Chi_Minh')
        : $from;

    $createdCount = app(AttendanceService::class)->syncMissingAttendanceRecords($from, $to);

    $this->info("Synced {$createdCount} missing attendance record(s).");

    if ((bool) $this->option('auto-close')) {
        $closedCount = app(AttendanceService::class)->closeUnexplainedAbsences(
            (int) config('attendance.auto_close_after_days', 2),
            Carbon::now('Asia/Ho_Chi_Minh')
        );

        $this->info("Closed {$closedCount} unexplained absence record(s) after sync.");
    }
})->purpose('Backfill missing attendance records for a date range and optionally auto-close overdue absences');

Artisan::command('positions:sync-capabilities-pivot {--dry-run}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $checked = 0;
    $updated = 0;

    $capabilityIdByCode = PositionCapabilityModel::query()
        ->pluck('id', 'code')
        ->toArray();

    Position::query()
        ->select(['id', 'name', 'capabilities'])
        ->chunkById(200, function ($positions) use (&$checked, &$updated, $dryRun, $capabilityIdByCode) {
            foreach ($positions as $position) {
                $checked++;

                $codes = PositionRoleResolver::normalizeCapabilities(
                    is_array($position->capabilities) ? $position->capabilities : []
                );

                $ids = [];
                foreach ($codes as $code) {
                    if (isset($capabilityIdByCode[$code])) {
                        $ids[] = (int) $capabilityIdByCode[$code];
                    }
                }

                $current = $position->capabilitiesCatalog()->pluck('position_capabilities.id')->map(fn ($id) => (int) $id)->all();
                sort($current);
                sort($ids);

                if ($current === $ids) {
                    continue;
                }

                $this->line("Position #{$position->id} {$position->name}: sync " . count($ids) . ' capability(s).');

                if (!$dryRun) {
                    $position->capabilitiesCatalog()->sync($ids);
                    $updated++;
                }
            }
        });

    $this->newLine();
    $this->info("Checked: {$checked}");
    $this->info($dryRun ? 'Dry-run: no data changed.' : "Updated: {$updated}");
})->purpose('Sync position capability pivot from positions.capabilities JSON');

Artisan::command('feedbacks:escalate-stale {--hours=}', function () {
    $configuredHours = (int) config('feedback.escalation_hours', 24);
    $hours = max(1, (int) ($this->option('hours') ?: $configuredHours));
    $count = app(FeedbackEscalationService::class)->escalateStaleFeedbacks($hours);

    $this->info("Escalated {$count} stale feedback message(s) older than {$hours} hour(s).");
})->purpose('Escalate unreplied feedback messages to the next superior level after a timeout');

Schedule::command('attendance:mark-absent ' . Carbon::now('Asia/Ho_Chi_Minh')->subDays((int) config('attendance.auto_mark_after_days', 2))->toDateString())
    ->dailyAt('09:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping();

Schedule::command('attendance:close-unexplained-absences --days=' . (int) config('attendance.auto_close_after_days', 2))
    ->dailyAt('09:10')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping();

Schedule::command('feedbacks:escalate-stale')->hourly();
