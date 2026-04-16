<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Chuan hoa gia tri attendance_status ve bo enum gon: on_time, late, absent.
        // present -> on_time.
        DB::statement("UPDATE attendance_records SET attendance_status = 'on_time' WHERE attendance_status = 'present'");
        // pending/half_day/leave -> absent theo quy uoc moi.
        DB::statement("UPDATE attendance_records SET attendance_status = 'absent' WHERE attendance_status IN ('pending','half_day','leave')");
        // Thu hep enum theo bo gia tri moi.
        DB::statement("ALTER TABLE attendance_records MODIFY attendance_status ENUM('on_time','late','absent') NOT NULL DEFAULT 'on_time'");
    }

    public function down(): void
    {
        // Khoi phuc enum cu.
        DB::statement("ALTER TABLE attendance_records MODIFY attendance_status ENUM('pending','present','late','absent','half_day','leave') NOT NULL DEFAULT 'pending'");
        // on_time -> present de tuong thich du lieu cu.
        DB::statement("UPDATE attendance_records SET attendance_status = 'present' WHERE attendance_status = 'on_time'");
    }
};
