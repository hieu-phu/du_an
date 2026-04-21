<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_types', 'prorate_by_hire_date')) {
                $table->boolean('prorate_by_hire_date')->default(true)->after('annual_quota');
            }

            if (!Schema::hasColumn('leave_types', 'carryover_limit')) {
                $table->decimal('carryover_limit', 8, 2)->nullable()->after('max_days_per_request');
            }
        });

        Schema::table('employee_leave_balances', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_leave_balances', 'carryover_expires_on')) {
                $table->date('carryover_expires_on')->nullable()->after('adjusted_days');
            }
        });

        Schema::table('attendance_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_requests', 'leave_duration_type')) {
                $table->string('leave_duration_type', 20)->nullable()->after('leave_days');
            }

            if (!Schema::hasColumn('attendance_requests', 'leave_hours')) {
                $table->decimal('leave_hours', 8, 2)->default(0)->after('leave_duration_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_requests', 'leave_hours')) {
                $table->dropColumn('leave_hours');
            }

            if (Schema::hasColumn('attendance_requests', 'leave_duration_type')) {
                $table->dropColumn('leave_duration_type');
            }
        });

        Schema::table('employee_leave_balances', function (Blueprint $table) {
            if (Schema::hasColumn('employee_leave_balances', 'carryover_expires_on')) {
                $table->dropColumn('carryover_expires_on');
            }
        });

        Schema::table('leave_types', function (Blueprint $table) {
            if (Schema::hasColumn('leave_types', 'carryover_limit')) {
                $table->dropColumn('carryover_limit');
            }

            if (Schema::hasColumn('leave_types', 'prorate_by_hire_date')) {
                $table->dropColumn('prorate_by_hire_date');
            }
        });
    }
};
