<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VietnamAdministrativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/asfycode_cms.sql');

        if (!File::exists($path)) {
            $this->command->error("File not found: $path");
            return;
        }

        $this->command->info("Importing Vietnam Administrative Units from asfycode_cms.sql...");

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate existing data to avoid conflicts
        DB::table('wards')->truncate();
        DB::table('provinces')->truncate();

        // Read the SQL file
        $sql = File::get($path);

        // We only want the INSERT statements.
        // The file contains CREATE TABLE and ALTER TABLE which we don't want as Laravel handles it.
        preg_match_all('/INSERT INTO `?provinces`?[^;]+;/i', $sql, $provinceInserts);
        preg_match_all('/INSERT INTO `?wards`?[^;]+;/i', $sql, $wardInserts);

        foreach ($provinceInserts[0] as $insert) {
            DB::unprepared($insert);
        }

        foreach ($wardInserts[0] as $insert) {
            DB::unprepared($insert);
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("Administrative units imported from asfycode_cms.sql successfully!");
    }
}
