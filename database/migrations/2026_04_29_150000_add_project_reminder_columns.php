<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->timestamp('deadline_reminded_at')->nullable()->after('completed_at');
        });

        Schema::table('project_implementation_subtasks', function (Blueprint $table) {
            $table->timestamp('deadline_reminded_at')->nullable()->after('actual_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->dropColumn('deadline_reminded_at');
        });

        Schema::table('project_implementation_subtasks', function (Blueprint $table) {
            $table->dropColumn('deadline_reminded_at');
        });
    }
};
