<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class CleanLunarHolidaysSeeder extends Seeder
{
    public function run(): void
    {
        Holiday::truncate();

        $year = 2026;
        $tet = ['2026-02-16', '2026-02-17', '2026-02-18', '2026-02-19', '2026-02-20'];
        
        foreach ($tet as $index => $date) {
            Holiday::create([
                'holiday_date' => $date,
                'holiday_name' => "Tết Nguyên Đán (Ngày " . ($index + 1) . ") $year",
                'holiday_type' => 'public',
                'is_paid_leave' => true,
            ]);
        }

        Holiday::create([
            'holiday_date' => '2026-04-26',
            'holiday_name' => "Giỗ tổ Hùng Vương $year",
            'holiday_type' => 'public',
            'is_paid_leave' => true,
        ]);

        $this->command->info('Holiday table cleaned and 2026 Lunar holidays seeded.');
    }
}
