<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->foreignId('default_work_shift_id')
                ->nullable()
                ->after('position_id')
                ->constrained('work_shifts')
                ->nullOnDelete();
        });

        Schema::table('work_shifts', function (Blueprint $table) {
            $table->unsignedInteger('late_grace_minutes')->default(0)->after('grace_minutes');
            $table->unsignedInteger('early_leave_grace_minutes')->default(0)->after('late_grace_minutes');
        });

        Schema::create('employee_work_shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->constrained('employee_profiles')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->foreignId('work_shift_id')->constrained('work_shifts')->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_profile_id', 'effective_from', 'effective_to'], 'emp_shift_assignment_emp_effective_idx');
            $table->index(['department_id', 'effective_from', 'effective_to'], 'emp_shift_assignment_dept_effective_idx');
        });

        Schema::table('attendance_events', function (Blueprint $table) {
            $table->text('device_info')->nullable()->after('ip_address');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->foreignId('work_shift_id')->nullable()->after('employee_profile_id')->constrained('work_shifts')->nullOnDelete();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('attendance_status');
            $table->enum('day_status', [
                'present',
                'late',
                'early_leave',
                'leave',
                'unpaid_leave',
                'business_trip',
                'missing_check_in',
                'missing_check_out',
                'absent',
            ])->default('present')->after('approval_status');
            $table->unsignedInteger('late_minutes')->default(0)->after('worked_minutes');
            $table->unsignedInteger('early_leave_minutes')->default(0)->after('late_minutes');
            $table->unsignedInteger('overtime_minutes')->default(0)->after('early_leave_minutes');
            $table->boolean('missing_check_in')->default(false)->after('overtime_minutes');
            $table->boolean('missing_check_out')->default(false)->after('missing_check_in');
            $table->string('check_in_ip_address', 45)->nullable()->after('check_in_at');
            $table->string('check_out_ip_address', 45)->nullable()->after('check_out_at');
            $table->text('check_in_device')->nullable()->after('check_in_ip_address');
            $table->text('check_out_device')->nullable()->after('check_out_ip_address');
            $table->json('shift_snapshot')->nullable()->after('note');
            $table->foreignId('rejected_by')->nullable()->after('confirmed_by')->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable()->after('confirmed_at');
            $table->text('approval_note')->nullable()->after('note');

            $table->index(['work_date', 'approval_status'], 'attendance_records_work_date_approval_status_idx');
            $table->index(['work_date', 'day_status'], 'attendance_records_work_date_day_status_idx');
        });

        Schema::create('attendance_month_locks', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->boolean('is_locked')->default(true);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year', 'department_id'], 'attendance_month_locks_unique_period_department');
        });

        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete();
            $table->foreignId('approval_request_id')->nullable()->constrained('approval_requests')->nullOnDelete();
            $table->enum('request_type', ['leave', 'late_early', 'forgot_check', 'business_trip', 'make_up']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('request_date')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->enum('leave_type', ['paid', 'unpaid'])->nullable();
            $table->string('requested_status', 50)->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_profile_id', 'request_type', 'status'], 'attendance_requests_emp_type_status_idx');
        });

        Schema::create('attendance_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_record_id')->constrained('attendance_records')->cascadeOnDelete();
            $table->foreignId('approval_request_id')->nullable()->constrained('approval_requests')->nullOnDelete();
            $table->dateTime('old_check_in_at')->nullable();
            $table->dateTime('new_check_in_at')->nullable();
            $table->dateTime('old_check_out_at')->nullable();
            $table->dateTime('new_check_out_at')->nullable();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();
        });

        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete();
            $table->foreignId('approval_request_id')->nullable()->constrained('approval_requests')->nullOnDelete();
            $table->date('work_date');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedInteger('requested_minutes')->default(0);
            $table->unsignedInteger('approved_minutes')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();

            $table->index(['employee_profile_id', 'work_date', 'status'], 'overtime_requests_emp_work_date_status_idx');
        });

        Schema::table('attendance_monthly_summaries', function (Blueprint $table) {
            $table->unsignedInteger('total_leave_days')->default(0)->after('total_absent_days');
            $table->unsignedInteger('total_unpaid_leave_days')->default(0)->after('total_leave_days');
            $table->unsignedInteger('total_business_trip_days')->default(0)->after('total_unpaid_leave_days');
            $table->unsignedInteger('total_overtime_minutes')->default(0)->after('total_early_leave_count');
        });

        DB::table('attendance_records')
            ->where('is_confirmed', true)
            ->update(['approval_status' => 'approved']);

        DB::table('attendance_records')
            ->where('is_confirmed', false)
            ->update(['approval_status' => 'pending']);

        DB::table('attendance_records')
            ->where('attendance_status', 'on_time')
            ->update(['day_status' => 'present']);

        DB::table('attendance_records')
            ->where('attendance_status', 'late')
            ->update(['day_status' => 'late']);

        DB::table('attendance_records')
            ->where('attendance_status', 'absent')
            ->update(['day_status' => 'absent']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_monthly_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'total_leave_days',
                'total_unpaid_leave_days',
                'total_business_trip_days',
                'total_overtime_minutes',
            ]);
        });

        Schema::dropIfExists('overtime_requests');
        Schema::dropIfExists('attendance_adjustments');
        Schema::dropIfExists('attendance_requests');
        Schema::dropIfExists('attendance_month_locks');

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex('attendance_records_work_date_approval_status_idx');
            $table->dropIndex('attendance_records_work_date_day_status_idx');
            $table->dropConstrainedForeignId('work_shift_id');
            $table->dropConstrainedForeignId('rejected_by');
            $table->dropColumn([
                'approval_status',
                'day_status',
                'late_minutes',
                'early_leave_minutes',
                'overtime_minutes',
                'missing_check_in',
                'missing_check_out',
                'check_in_ip_address',
                'check_out_ip_address',
                'check_in_device',
                'check_out_device',
                'shift_snapshot',
                'rejected_at',
                'approval_note',
            ]);
        });

        Schema::table('attendance_events', function (Blueprint $table) {
            $table->dropColumn('device_info');
        });

        Schema::dropIfExists('employee_work_shift_assignments');

        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropColumn(['late_grace_minutes', 'early_leave_grace_minutes']);
        });

        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_work_shift_id');
        });
    }
};
