<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_snapshots', function (Blueprint $table) {
            $table->decimal('allowance_amount', 15, 2)->default(0)->after('overtime_amount');
            $table->decimal('attendance_deduction_amount', 15, 2)->default(0)->after('pending_amount');
            $table->decimal('manual_deduction_amount', 15, 2)->default(0)->after('attendance_deduction_amount');
            $table->unsignedInteger('warning_count')->default(0)->after('net_amount');
        });
    }

    public function down(): void
    {
        Schema::table('salary_snapshots', function (Blueprint $table) {
            $table->dropColumn([
                'allowance_amount',
                'attendance_deduction_amount',
                'manual_deduction_amount',
                'warning_count',
            ]);
        });
    }
};
