<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->string('shift_code', 50)->nullable()->unique()->after('id');
            $table->time('break_start_time')->nullable()->after('end_time');
            $table->time('break_end_time')->nullable()->after('break_start_time');
            $table->unsignedInteger('half_day_minutes')->default(240)->after('standard_minutes');
            $table->boolean('allows_overtime')->default(true)->after('early_leave_grace_minutes');
            $table->boolean('is_overnight')->default(false)->after('allows_overtime');
            $table->text('description')->nullable()->after('is_overnight');
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->string('holiday_type', 50)->default('public')->after('holiday_name');
            $table->boolean('is_recurring')->default(false)->after('is_paid_leave');
            $table->text('note')->nullable()->after('is_recurring');
        });

        Schema::table('employee_work_shift_assignments', function (Blueprint $table) {
            $table->json('weekdays')->nullable()->after('effective_to');
        });
    }

    public function down(): void
    {
        Schema::table('employee_work_shift_assignments', function (Blueprint $table) {
            $table->dropColumn('weekdays');
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn(['holiday_type', 'is_recurring', 'note']);
        });

        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropUnique(['shift_code']);
            $table->dropColumn([
                'shift_code',
                'break_start_time',
                'break_end_time',
                'half_day_minutes',
                'allows_overtime',
                'is_overnight',
                'description',
            ]);
        });
    }
};
