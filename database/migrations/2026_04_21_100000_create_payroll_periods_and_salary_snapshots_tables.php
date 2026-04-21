<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('status')->default('draft');
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year']);
        });

        Schema::create('salary_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete();
            $table->string('employee_code')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('department_name')->nullable();
            $table->string('position_name')->nullable();
            $table->string('currency', 10)->default('VND');
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('approved_work_units', 8, 2)->default(0);
            $table->unsignedInteger('approved_overtime_minutes')->default(0);
            $table->decimal('base_salary_amount', 15, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->decimal('pending_amount', 15, 2)->default(0);
            $table->decimal('deduction_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->json('payload');
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_profile_id'], 'salary_snapshots_period_emp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_snapshots');
        Schema::dropIfExists('payroll_periods');
    }
};
