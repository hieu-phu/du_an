<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_shift_overtime_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_shift_id')->unique()->constrained('work_shifts')->cascadeOnDelete();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('hourly_rate', 12, 2)->nullable();
            $table->timestamps();
        });

        $rows = DB::table('work_shifts')
            ->where(function ($query) {
                $query
                    ->whereNotNull('overtime_start_time')
                    ->orWhereNotNull('overtime_end_time')
                    ->orWhereNotNull('overtime_hourly_rate');
            })
            ->select(['id', 'overtime_start_time', 'overtime_end_time', 'overtime_hourly_rate'])
            ->get();

        $now = now();

        foreach ($rows as $row) {
            DB::table('work_shift_overtime_rules')->updateOrInsert(
                ['work_shift_id' => $row->id],
                [
                    'start_time' => $row->overtime_start_time,
                    'end_time' => $row->overtime_end_time,
                    'hourly_rate' => $row->overtime_hourly_rate,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('work_shift_overtime_rules');
    }
};
