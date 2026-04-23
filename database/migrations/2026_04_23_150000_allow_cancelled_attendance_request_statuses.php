<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('attendance_requests') && Schema::hasColumn('attendance_requests', 'status')) {
            DB::statement("ALTER TABLE attendance_requests MODIFY status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending'");
        }

        if (Schema::hasTable('overtime_requests') && Schema::hasColumn('overtime_requests', 'status')) {
            DB::statement("ALTER TABLE overtime_requests MODIFY status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('attendance_requests') && Schema::hasColumn('attendance_requests', 'status')) {
            DB::table('attendance_requests')->where('status', 'cancelled')->update(['status' => 'rejected']);
            DB::statement("ALTER TABLE attendance_requests MODIFY status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        }

        if (Schema::hasTable('overtime_requests') && Schema::hasColumn('overtime_requests', 'status')) {
            DB::table('overtime_requests')->where('status', 'cancelled')->update(['status' => 'rejected']);
            DB::statement("ALTER TABLE overtime_requests MODIFY status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        }
    }
};
