<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class PositionCapabilitySeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('position_capabilities')) {
            $this->command?->warn('Table position_capabilities does not exist. Skipping position capability seed.');
            return;
        }

        $path = database_path('hrm_position_capabilities_seed.sql');

        if (!File::exists($path)) {
            $this->command?->error("File not found: {$path}");
            return;
        }

        $this->command?->info('Importing HRM position capabilities...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            foreach ($this->statements(File::get($path)) as $statement) {
                DB::unprepared($statement);
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command?->info('HRM position capabilities imported successfully.');
    }

    /**
     * @return array<int, string>
     */
    private function statements(string $sql): array
    {
        $sql = trim($sql);

        if ($sql === '') {
            return [];
        }

        return array_values(array_filter(
            array_map(
                static fn (string $statement): string => trim($statement),
                explode(';', $sql)
            ),
            static fn (string $statement): bool => $statement !== ''
        ));
    }
}
