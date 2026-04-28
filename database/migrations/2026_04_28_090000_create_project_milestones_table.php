<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('phase_name', 120);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->date('completed_at')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'phase_name']);
            $table->index(['project_id', 'sort_order']);
        });

        Schema::table('project_implementation_details', function (Blueprint $table) {
            $table->foreignId('project_milestone_id')
                ->nullable()
                ->after('project_id')
                ->constrained('project_milestones')
                ->nullOnDelete();

            $table->index(['project_id', 'project_milestone_id'], 'project_impl_details_project_milestone_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_implementation_details', function (Blueprint $table) {
            $table->dropIndex('project_impl_details_project_milestone_idx');
            $table->dropConstrainedForeignId('project_milestone_id');
        });

        Schema::dropIfExists('project_milestones');
    }
};
