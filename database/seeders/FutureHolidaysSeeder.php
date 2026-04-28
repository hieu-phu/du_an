<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FutureHolidaysSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing holidays to avoid duplicates if preferred
        // Holiday::truncate();

        $holidays = [];

        for ($year = 2025; $year <= 2030; $year++) {
            // 1. New Year
            $holidays[] = [
                'holiday_date' => "$year-01-01",
                'holiday_name' => "Tết Dương lịch $year",
                'holiday_type' => 'public',
                'is_paid_leave' => true,
            ];

            // 2. Liberation Day
            $holidays[] = [
                'holiday_date' => "$year-04-30",
                'holiday_name' => "Ngày Giải phóng Miền Nam $year",
                'holiday_type' => 'public',
                'is_paid_leave' => true,
            ];

            // 3. International Labor Day
            $holidays[] = [
                'holiday_date' => "$year-05-01",
                'holiday_name' => "Ngày Quốc tế Lao động $year",
                'holiday_type' => 'public',
                'is_paid_leave' => true,
            ];

            // 4. National Day (2 days)
            $holidays[] = [
                'holiday_date' => "$year-09-02",
                'holiday_name' => "Quốc khánh $year",
                'holiday_type' => 'public',
                'is_paid_leave' => true,
            ];
            $holidays[] = [
                'holiday_date' => "$year-09-03",
                'holiday_name' => "Quốc khánh (Ngày 2) $year",
                'holiday_type' => 'public',
                'is_paid_leave' => true,
            ];

            // 5. Lunar Based Holidays (Tet & Hung King)
            $lunarHolidays = $this->getLunarHolidays($year);
            foreach ($lunarHolidays as $lunar) {
                $holidays[] = $lunar;
            }
        }

        foreach ($holidays as $data) {
            Holiday::query()->updateOrCreate(
                ['holiday_date' => $data['holiday_date']],
                $data
            );
        }

        $this->command->info('Holidays from 2025 to 2030 have been seeded successfully.');
    }

    private function getLunarHolidays(int $year): array
    {
        $data = [
            2025 => [
                'tet' => ['2025-01-27', '2025-01-28', '2025-01-29', '2025-01-30', '2025-01-31'],
                'hung_king' => '2025-04-07',
            ],
            2026 => [
                'tet' => ['2026-02-16', '2026-02-17', '2026-02-18', '2026-02-19', '2026-02-20'],
                'hung_king' => '2026-04-26',
            ],
            2027 => [
                'tet' => ['2027-02-05', '2027-02-06', '2027-02-07', '2027-02-08', '2027-02-09'],
                'hung_king' => '2027-04-15',
            ],
            2028 => [
                'tet' => ['2028-01-25', '2028-01-26', '2028-01-27', '2028-01-28', '2028-01-29'],
                'hung_king' => '2028-04-04',
            ],
            2029 => [
                'tet' => ['2029-02-12', '2029-02-13', '2029-02-14', '2029-02-15', '2029-02-16'],
                'hung_king' => '2029-04-23',
            ],
            2030 => [
                'tet' => ['2030-02-02', '2030-02-03', '2030-02-04', '2030-02-05', '2030-02-06'],
                'hung_king' => '2030-04-12',
            ],
        ];

        $current = $data[$year] ?? null;
        if (!$current) return [];

        $list = [];
        foreach ($current['tet'] as $index => $date) {
            $list[] = [
                'holiday_date' => $date,
                'holiday_name' => "Tết Nguyên Đán (Ngày " . ($index + 1) . ") $year",
                'holiday_type' => 'public',
                'is_paid_leave' => true,
            ];
        }

        $list[] = [
            'holiday_date' => $current['hung_king'],
            'holiday_name' => "Giỗ tổ Hùng Vương $year",
            'holiday_type' => 'public',
            'is_paid_leave' => true,
        ];

        return $list;
    }
}
