<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->time('overtime_start_time')->nullable()->after('break_end_time');
            $table->time('overtime_end_time')->nullable()->after('overtime_start_time');
            $table->unsignedInteger('handover_break_minutes')->default(0)->after('half_day_minutes');
            $table->decimal('overtime_hourly_rate', 12, 2)->nullable()->after('handover_break_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropColumn([
                'overtime_start_time',
                'overtime_end_time',
                'handover_break_minutes',
                'overtime_hourly_rate',
            ]);
        });
    }
};
