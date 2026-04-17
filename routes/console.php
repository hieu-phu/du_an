<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Position;
use App\Models\PositionCapability as PositionCapabilityModel;
use App\Services\AttendanceService;
use App\Support\PositionRoleResolver;
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
