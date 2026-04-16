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
        // Bo sung ca lam mac dinh cho tung nhan vien.
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->foreignId('default_work_shift_id')
                ->nullable()
                ->after('position_id')
                ->constrained('work_shifts')
                ->nullOnDelete();
        });

        // Mo rong cau hinh ca lam de tach grace di muon va ve som.
        Schema::table('work_shifts', function (Blueprint $table) {
            // So phut tre toi da van duoc tinh dung gio vao ca.
            $table->unsignedInteger('late_grace_minutes')->default(0)->after('grace_minutes');
            // So phut ve som toi da van duoc tinh du ca.
            $table->unsignedInteger('early_leave_grace_minutes')->default(0)->after('late_grace_minutes');
        });

        // Bang gan ca lam theo nhan vien hoac theo phong ban theo tung giai doan.
        Schema::create('employee_work_shift_assignments', function (Blueprint $table) {
            $table->id(); // Khoa chinh ban ghi gan ca.
            $table->foreignId('employee_profile_id')->nullable()->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien ap dung ca, null neu ap dung theo phong ban.
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete(); // Phong ban ap dung ca, null neu ap dung theo nhan vien.
            $table->foreignId('work_shift_id')->constrained('work_shifts')->cascadeOnDelete(); // Ca lam duoc gan.
            $table->date('effective_from'); // Ngay bat dau hieu luc.
            $table->date('effective_to')->nullable(); // Ngay ket thuc hieu luc; null = con hieu luc mo.
            $table->boolean('is_active')->default(true); // Co dang su dung ban ghi gan ca hay khong.
            $table->text('note')->nullable(); // Ghi chu phat sinh khi gan ca.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi tao ban ghi gan ca.
            $table->timestamps(); // created_at, updated_at.

            // Toi uu tim ca lam hieu luc theo nhan vien va khoang ngay.
            $table->index(['employee_profile_id', 'effective_from', 'effective_to'], 'emp_shift_assignment_emp_effective_idx');
            // Toi uu tim ca lam hieu luc theo phong ban va khoang ngay.
            $table->index(['department_id', 'effective_from', 'effective_to'], 'emp_shift_assignment_dept_effective_idx');

            $table->comment('Bang phan ca linh hoat theo nhan vien/phong ban.');
        });

        // Bo sung thong tin thiet bi tu su kien cham cong.
        Schema::table('attendance_events', function (Blueprint $table) {
            $table->text('device_info')->nullable()->after('ip_address');
        });

        // Mo rong bang tong hop cong ngay de ho tro duyet, OT, va truy vet chi tiet.
        Schema::table('attendance_records', function (Blueprint $table) {
            // Ca lam duoc ap dung cho ban ghi cong ngay.
            $table->foreignId('work_shift_id')->nullable()->after('employee_profile_id')->constrained('work_shifts')->nullOnDelete();
            // Trang thai duyet cong: cho duyet/da duyet/tu choi.
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('attendance_status');
            // Trang thai nghiep vu ngay cong sau khi tinh toan.
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
            // So phut di muon cua ngay.
            $table->unsignedInteger('late_minutes')->default(0)->after('worked_minutes');
            // So phut ve som cua ngay.
            $table->unsignedInteger('early_leave_minutes')->default(0)->after('late_minutes');
            // So phut tang ca duoc ghi nhan.
            $table->unsignedInteger('overtime_minutes')->default(0)->after('early_leave_minutes');
            // Co thieu check-in hay khong.
            $table->boolean('missing_check_in')->default(false)->after('overtime_minutes');
            // Co thieu check-out hay khong.
            $table->boolean('missing_check_out')->default(false)->after('missing_check_in');
            // IP check-in de doi soat.
            $table->string('check_in_ip_address', 45)->nullable()->after('check_in_at');
            // IP check-out de doi soat.
            $table->string('check_out_ip_address', 45)->nullable()->after('check_out_at');
            // Thiet bi check-in.
            $table->text('check_in_device')->nullable()->after('check_in_ip_address');
            // Thiet bi check-out.
            $table->text('check_out_device')->nullable()->after('check_out_ip_address');
            // Snapshot cau hinh ca/nguong tinh cong tai thoi diem tinh cong.
            $table->json('shift_snapshot')->nullable()->after('note');
            // Nguoi tu choi phe duyet cong.
            $table->foreignId('rejected_by')->nullable()->after('confirmed_by')->constrained('users')->nullOnDelete();
            // Thoi diem tu choi.
            $table->timestamp('rejected_at')->nullable()->after('confirmed_at');
            // Ly do/ghi chu phe duyet hoac tu choi.
            $table->text('approval_note')->nullable()->after('note');

            // Toi uu man hinh duyet cong theo ngay.
            $table->index(['work_date', 'approval_status'], 'attendance_records_work_date_approval_status_idx');
            // Toi uu tong hop ngay cong theo day_status.
            $table->index(['work_date', 'day_status'], 'attendance_records_work_date_day_status_idx');
        });

        // Bang khoa bang cong theo thang (co the theo phong ban).
        Schema::create('attendance_month_locks', function (Blueprint $table) {
            $table->id(); // Khoa chinh ban ghi khoa thang.
            $table->unsignedTinyInteger('month'); // Thang khoa cong.
            $table->unsignedSmallInteger('year'); // Nam khoa cong.
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete(); // Khoa theo phong ban; null = khoa toan cong ty.
            $table->boolean('is_locked')->default(true); // Trang thai khoa hien tai.
            $table->timestamp('locked_at')->nullable(); // Thoi diem khoa.
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi khoa.
            $table->timestamp('unlocked_at')->nullable(); // Thoi diem mo khoa.
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi mo khoa.
            $table->text('note')->nullable(); // Ghi chu nghiep vu khoa/mo khoa.
            $table->timestamps(); // created_at, updated_at.

            $table->unique(['month', 'year', 'department_id'], 'attendance_month_locks_unique_period_department');
            $table->comment('Bang khoa bang cong theo ky thang va phong ban.');
        });

        // Bang don de nghi lien quan den cham cong.
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id(); // Khoa chinh don.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien tao don.
            $table->foreignId('approval_request_id')->nullable()->constrained('approval_requests')->nullOnDelete(); // Phieu phe duyet tong the neu co.
            $table->enum('request_type', ['leave', 'late_early', 'forgot_check', 'business_trip', 'make_up']); // Loai don nghiep vu cham cong.
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Trang thai xu ly don.
            $table->date('request_date')->nullable(); // Ngay ap dung voi don theo ngay le.
            $table->date('from_date')->nullable(); // Tu ngay.
            $table->date('to_date')->nullable(); // Den ngay.
            $table->time('from_time')->nullable(); // Tu gio.
            $table->time('to_time')->nullable(); // Den gio.
            $table->enum('leave_type', ['paid', 'unpaid'])->nullable(); // Loai nghi co/khong luong.
            $table->string('requested_status', 50)->nullable(); // Trang thai cong mong muon sau khi duyet.
            $table->string('attachment_path')->nullable(); // Tep dinh kem minh chung.
            $table->text('reason')->nullable(); // Ly do gui don.
            $table->timestamp('applied_at')->nullable(); // Thoi diem gui don.
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi tao don thay (neu co).
            $table->timestamps(); // created_at, updated_at.

            $table->index(['employee_profile_id', 'request_type', 'status'], 'attendance_requests_emp_type_status_idx');
            $table->comment('Bang luu don de nghi cham cong cua nhan vien.');
        });

        // Bang de nghi dieu chinh check-in/check-out.
        Schema::create('attendance_adjustments', function (Blueprint $table) {
            $table->id(); // Khoa chinh de nghi dieu chinh cong.
            $table->foreignId('attendance_record_id')->constrained('attendance_records')->cascadeOnDelete(); // Ban ghi cong can dieu chinh.
            $table->foreignId('approval_request_id')->nullable()->constrained('approval_requests')->nullOnDelete(); // Phieu phe duyet tong the neu co.
            $table->dateTime('old_check_in_at')->nullable(); // Gio vao hien tai truoc dieu chinh.
            $table->dateTime('new_check_in_at')->nullable(); // Gio vao de nghi moi.
            $table->dateTime('old_check_out_at')->nullable(); // Gio ra hien tai truoc dieu chinh.
            $table->dateTime('new_check_out_at')->nullable(); // Gio ra de nghi moi.
            $table->text('reason'); // Ly do dieu chinh.
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Trang thai de nghi.
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete(); // Nguoi yeu cau dieu chinh.
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi duyet/tu choi.
            $table->timestamp('reviewed_at')->nullable(); // Thoi diem review.
            $table->text('review_note')->nullable(); // Ghi chu review.
            $table->timestamps(); // created_at, updated_at.

            $table->comment('Bang luu de nghi sua cong check-in/check-out.');
        });

        // Bang de nghi tang ca.
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id(); // Khoa chinh de nghi OT.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien de nghi OT.
            $table->foreignId('approval_request_id')->nullable()->constrained('approval_requests')->nullOnDelete(); // Phieu phe duyet tong the.
            $table->date('work_date'); // Ngay lam viec co OT.
            $table->dateTime('start_at'); // Gio bat dau OT.
            $table->dateTime('end_at'); // Gio ket thuc OT.
            $table->unsignedInteger('requested_minutes')->default(0); // So phut OT de nghi.
            $table->unsignedInteger('approved_minutes')->default(0); // So phut OT duoc duyet.
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Trang thai de nghi.
            $table->text('reason')->nullable(); // Ly do OT.
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete(); // Nguoi tao de nghi.
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi duyet/tu choi.
            $table->timestamp('reviewed_at')->nullable(); // Thoi diem review.
            $table->text('review_note')->nullable(); // Ghi chu review.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['employee_profile_id', 'work_date', 'status'], 'overtime_requests_emp_work_date_status_idx');
            $table->comment('Bang luu de nghi tang ca va ket qua duyet.');
        });

        // Mo rong bang tong hop thang de bao cao day du hon.
        Schema::table('attendance_monthly_summaries', function (Blueprint $table) {
            // Tong so ngay nghi co luong.
            $table->unsignedInteger('total_leave_days')->default(0)->after('total_absent_days');
            // Tong so ngay nghi khong luong.
            $table->unsignedInteger('total_unpaid_leave_days')->default(0)->after('total_leave_days');
            // Tong so ngay cong tac.
            $table->unsignedInteger('total_business_trip_days')->default(0)->after('total_unpaid_leave_days');
            // Tong phut tang ca duoc ghi nhan trong thang.
            $table->unsignedInteger('total_overtime_minutes')->default(0)->after('total_early_leave_count');
        });

        // Dong bo du lieu cu sang approval_status moi.
        DB::table('attendance_records')
            ->where('is_confirmed', true)
            ->update(['approval_status' => 'approved']);

        DB::table('attendance_records')
            ->where('is_confirmed', false)
            ->update(['approval_status' => 'pending']);

        // Mapping attendance_status cu sang day_status moi.
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
        // Hoan tac cac cot thong ke thang bo sung.
        Schema::table('attendance_monthly_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'total_leave_days',
                'total_unpaid_leave_days',
                'total_business_trip_days',
                'total_overtime_minutes',
            ]);
        });

        // Xoa cac bang mo rong tinh nang attendance.
        Schema::dropIfExists('overtime_requests');
        Schema::dropIfExists('attendance_adjustments');
        Schema::dropIfExists('attendance_requests');
        Schema::dropIfExists('attendance_month_locks');

        // Hoan tac cot/index bo sung tren attendance_records.
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

        // Hoan tac cot thong tin thiet bi su kien cong.
        Schema::table('attendance_events', function (Blueprint $table) {
            $table->dropColumn('device_info');
        });

        // Hoan tac bang gan ca.
        Schema::dropIfExists('employee_work_shift_assignments');

        // Hoan tac cot grace mo rong tren work_shifts.
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropColumn(['late_grace_minutes', 'early_leave_grace_minutes']);
        });

        // Hoan tac default_work_shift_id tren ho so nhan vien.
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_work_shift_id');
        });
    }
};
