<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE attendance_records SET attendance_status = 'on_time' WHERE attendance_status = 'present'");
        DB::statement("UPDATE attendance_records SET attendance_status = 'absent' WHERE attendance_status IN ('pending','half_day','leave')");
        DB::statement("ALTER TABLE attendance_records MODIFY attendance_status ENUM('on_time','late','absent') NOT NULL DEFAULT 'on_time'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE attendance_records MODIFY attendance_status ENUM('pending','present','late','absent','half_day','leave') NOT NULL DEFAULT 'pending'");
        DB::statement("UPDATE attendance_records SET attendance_status = 'present' WHERE attendance_status = 'on_time'");
    }
};
