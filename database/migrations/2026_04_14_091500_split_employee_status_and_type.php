<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tach "trang thai lam viec" va "loai hinh lao dong" thanh 2 truong rieng.
        Schema::table('employee_profiles', function (Blueprint $table) {
            // Loai nhan su: thu viec, chinh thuc, intern, cong tac vien.
            $table->string('employment_type', 30)->default('official')->after('employment_status');
        });

        // Chuyen du lieu cu "probation" tu employment_status sang employment_type.
        DB::table('employee_profiles')
            ->where('employment_status', 'probation')
            ->update([
                'employment_status' => 'active',
                'employment_type' => 'probation',
            ]);

        // Chuyen "on_leave" ve nhom status inactive de don gian hoa status chinh.
        DB::table('employee_profiles')
            ->where('employment_status', 'on_leave')
            ->update([
                'employment_status' => 'inactive',
                'employment_type' => 'official',
            ]);

        // Dong bo lai employment_type cho cac ban ghi con lai.
        DB::table('employee_profiles')
            ->whereIn('employment_status', ['active', 'inactive', 'terminated'])
            ->update([
                'employment_type' => DB::raw("CASE WHEN employment_type = 'official' THEN 'official' ELSE employment_type END"),
            ]);

        // Chuan hoa enum moi cua employment_status va employment_type.
        DB::statement("ALTER TABLE employee_profiles MODIFY employment_status ENUM('active','inactive','terminated') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE employee_profiles MODIFY employment_type ENUM('probation','official','intern','collaborator') NOT NULL DEFAULT 'official'");
    }

    public function down(): void
    {
        // Khoi phuc enum cu truoc khi bo cot employment_type.
        DB::statement("ALTER TABLE employee_profiles MODIFY employment_status ENUM('active','inactive','probation','on_leave','terminated') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE employee_profiles MODIFY employment_type VARCHAR(30) NOT NULL DEFAULT 'official'");

        // Dua nhung ban ghi probation tro lai status cu.
        DB::table('employee_profiles')
            ->where('employment_type', 'probation')
            ->update([
                'employment_status' => 'probation',
                'employment_type' => 'official',
            ]);

        // Xoa cot bo sung khi rollback.
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn('employment_type');
        });
    }
};
