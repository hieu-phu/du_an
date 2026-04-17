<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('authority_levels') || !Schema::hasColumn('authority_levels', 'minimum_role')) {
            return;
        }

        Schema::table('authority_levels', function (Blueprint $table) {
            $table->dropColumn('minimum_role');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('authority_levels') || Schema::hasColumn('authority_levels', 'minimum_role')) {
            return;
        }
        Schema::table('authority_levels', function (Blueprint $table) {
            $table->string('minimum_role', 20)->default('rank')->after('name');
        });
    }
};
