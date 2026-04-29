<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_implementation_detail_id')
                ->constrained('project_implementation_details')
                ->cascadeOnDelete();
            $table->foreignId('project_implementation_subtask_id')
                ->constrained('project_implementation_subtasks')
                ->cascadeOnDelete();
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete();
            $table->date('work_date');
            $table->decimal('hours', 5, 2);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'work_date'], 'project_work_logs_project_date_idx');
            $table->index(['employee_profile_id', 'work_date'], 'project_work_logs_employee_date_idx');
            $table->index('project_implementation_subtask_id', 'project_work_logs_subtask_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_work_logs');
    }
};
