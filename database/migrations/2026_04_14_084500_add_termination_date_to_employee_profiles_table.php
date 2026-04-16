<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bo sung ngay nghi viec de theo doi qua trinh ket thuc hop dong.
        Schema::table('employee_profiles', function (Blueprint $table) {
            // Ngay nhan vien chinh thuc nghi viec; null neu van dang lam.
            $table->date('termination_date')->nullable()->after('hire_date');
        });
    }

    public function down(): void
    {
        // Hoan tac truong ngay nghi viec.
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn('termination_date');
        });
    }
};
