<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('project_implementation_subtasks');

        Schema::create('project_implementation_subtasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_implementation_detail_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->unsignedInteger('duration_days')->default(1);
            $table->date('due_date');
            $table->date('actual_end_date')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('project_implementation_detail_id', 'proj_subtasks_detail_fk')
                ->references('id')
                ->on('project_implementation_details')
                ->cascadeOnDelete();
            $table->foreign('project_id', 'proj_subtasks_project_fk')
                ->references('id')
                ->on('projects')
                ->cascadeOnDelete();
            $table->foreign('assigned_to', 'proj_subtasks_assignee_fk')
                ->references('id')
                ->on('employee_profiles')
                ->nullOnDelete();
            $table->index(['project_implementation_detail_id', 'status'], 'proj_subtasks_detail_status_idx');
            $table->index(['project_id', 'due_date'], 'proj_subtasks_project_due_idx');
            $table->index(['assigned_to', 'start_date'], 'proj_subtasks_assignee_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_implementation_subtasks');
    }
};
