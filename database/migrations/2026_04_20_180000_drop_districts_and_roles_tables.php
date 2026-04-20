<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('employee_profiles', 'district_id')) {
            Schema::table('employee_profiles', function (Blueprint $table) {
                $table->dropConstrainedForeignId('district_id');
            });
        }

        if (Schema::hasColumn('wards', 'district_id')) {
            Schema::table('wards', function (Blueprint $table) {
                $table->dropConstrainedForeignId('district_id');
            });
        }

        Schema::dropIfExists('districts');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('roles');
    }

    public function down(): void
    {
        if (!Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
                $table->string('name');
                $table->string('code', 20)->nullable()->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['province_id', 'name']);
                $table->index(['province_id', 'is_active']);
            });
        }

        if (!Schema::hasColumn('employee_profiles', 'district_id')) {
            Schema::table('employee_profiles', function (Blueprint $table) {
                $table->foreignId('district_id')->nullable()->after('province_id')->constrained('districts')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('wards', 'district_id')) {
            Schema::table('wards', function (Blueprint $table) {
                $table->foreignId('district_id')->nullable()->after('province_id')->constrained('districts')->nullOnDelete();
                $table->index(['district_id', 'name']);
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', static function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }

        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', static function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            });
        }

        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', static function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');

                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
            });
        }
    }
};
