<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_requests', 'business_trip_location')) {
                $table->string('business_trip_location')->nullable()->after('requested_status');
            }

            if (!Schema::hasColumn('attendance_requests', 'make_up_related_leave_date')) {
                $table->date('make_up_related_leave_date')->nullable()->after('business_trip_location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_requests', 'make_up_related_leave_date')) {
                $table->dropColumn('make_up_related_leave_date');
            }

            if (Schema::hasColumn('attendance_requests', 'business_trip_location')) {
                $table->dropColumn('business_trip_location');
            }
        });
    }
};
