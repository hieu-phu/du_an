<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('employment_type', 30)->default('official')->after('employment_status');
        });

        DB::table('employee_profiles')
            ->where('employment_status', 'probation')
            ->update([
                'employment_status' => 'active',
                'employment_type' => 'probation',
            ]);

        DB::table('employee_profiles')
            ->where('employment_status', 'on_leave')
            ->update([
                'employment_status' => 'inactive',
                'employment_type' => 'official',
            ]);

        DB::table('employee_profiles')
            ->whereIn('employment_status', ['active', 'inactive', 'terminated'])
            ->update([
                'employment_type' => DB::raw("CASE WHEN employment_type = 'official' THEN 'official' ELSE employment_type END"),
            ]);

        DB::statement("ALTER TABLE employee_profiles MODIFY employment_status ENUM('active','inactive','terminated') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE employee_profiles MODIFY employment_type ENUM('probation','official','intern','collaborator') NOT NULL DEFAULT 'official'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE employee_profiles MODIFY employment_status ENUM('active','inactive','probation','on_leave','terminated') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE employee_profiles MODIFY employment_type VARCHAR(30) NOT NULL DEFAULT 'official'");

        DB::table('employee_profiles')
            ->where('employment_type', 'probation')
            ->update([
                'employment_status' => 'probation',
                'employment_type' => 'official',
            ]);

        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn('employment_type');
        });
    }
};
