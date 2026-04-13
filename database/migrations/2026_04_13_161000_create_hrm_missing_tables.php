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
        // Bang lich su dang nhap de audit bao mat.
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id(); // Khoa chinh lich su dang nhap.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Tai khoan da dang nhap.
            $table->timestamp('login_at'); // Thoi diem dang nhap.
            $table->timestamp('logout_at')->nullable(); // Thoi diem dang xuat.
            $table->ipAddress('ip_address')->nullable(); // IP cua phien dang nhap.
            $table->string('device')->nullable(); // Ten thiet bi hoac he dieu hanh.
            $table->string('browser')->nullable(); // Trinh duyet su dung.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['user_id', 'login_at']); // Toi uu lich su dang nhap theo user.
        });

        // Bang ghi nhan bien dong trang thai lam viec cua nhan vien.
        Schema::create('employee_status_logs', function (Blueprint $table) {
            $table->id(); // Khoa chinh log.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien bi thay doi trang thai.
            $table->string('old_status')->nullable(); // Trang thai cu.
            $table->string('new_status'); // Trang thai moi.
            $table->text('reason')->nullable(); // Ly do thay doi.
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi thuc hien thay doi.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang lich su chuyen phong ban.
        Schema::create('employee_department_histories', function (Blueprint $table) {
            $table->id(); // Khoa chinh lich su.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien duoc dieu chuyen.
            $table->foreignId('old_department_id')->nullable()->constrained('departments')->nullOnDelete(); // Phong ban cu.
            $table->foreignId('new_department_id')->constrained('departments')->cascadeOnDelete(); // Phong ban moi.
            $table->timestamp('changed_at'); // Thoi diem dieu chuyen.
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi phe duyet/thuc hien dieu chuyen.
            $table->text('reason')->nullable(); // Ly do dieu chuyen.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang lich su thay doi chuc vu.
        Schema::create('employee_position_histories', function (Blueprint $table) {
            $table->id(); // Khoa chinh lich su.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien duoc thay doi chuc vu.
            $table->foreignId('old_position_id')->nullable()->constrained('positions')->nullOnDelete(); // Chuc vu cu.
            $table->foreignId('new_position_id')->constrained('positions')->cascadeOnDelete(); // Chuc vu moi.
            $table->timestamp('changed_at'); // Thoi diem thay doi.
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi thuc hien thay doi.
            $table->text('reason')->nullable(); // Ly do thay doi chuc vu.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang lich su thay doi luong.
        Schema::create('salary_histories', function (Blueprint $table) {
            $table->id(); // Khoa chinh lich su luong.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien duoc dieu chinh luong.
            $table->decimal('old_salary', 15, 2)->default(0); // Muc luong truoc dieu chinh.
            $table->decimal('new_salary', 15, 2); // Muc luong moi.
            $table->string('currency', 10)->default('VND'); // Don vi tien te.
            $table->date('effective_date'); // Ngay bat dau ap dung muc luong moi.
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi phe duyet.
            $table->text('note')->nullable(); // Ghi chu bo sung.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang ghi nhan cac lan phe duyet lien quan den cham cong.
        Schema::create('attendance_approvals', function (Blueprint $table) {
            $table->id(); // Khoa chinh phe duyet cong.
            $table->foreignId('attendance_record_id')->constrained('attendance_records')->cascadeOnDelete(); // Ban ghi cham cong duoc phe duyet.
            $table->string('approval_type'); // Loai phe duyet: manual_update, overtime_approval...
            $table->foreignId('approved_by')->constrained('users')->cascadeOnDelete(); // Nguoi phe duyet.
            $table->timestamp('approved_at'); // Thoi diem phe duyet.
            $table->string('status')->default('approved'); // Ket qua phe duyet: approved, rejected...
            $table->text('note')->nullable(); // Ghi chu phe duyet.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang tong hop cong theo thang.
        Schema::create('attendance_monthly_summaries', function (Blueprint $table) {
            $table->id(); // Khoa chinh tong hop thang.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien duoc tong hop.
            $table->unsignedTinyInteger('month'); // Thang tong hop.
            $table->unsignedSmallInteger('year'); // Nam tong hop.
            $table->unsignedInteger('total_working_days')->default(0); // Tong so ngay cong chuan trong thang.
            $table->decimal('total_present_days', 5, 2)->default(0); // Tong cong di lam.
            $table->decimal('total_absent_days', 5, 2)->default(0); // Tong cong vang mat.
            $table->unsignedInteger('total_late_count')->default(0); // So lan di muon.
            $table->unsignedInteger('total_early_leave_count')->default(0); // So lan ve som.
            $table->timestamps(); // created_at, updated_at.

            $table->unique(['employee_profile_id', 'month', 'year'], 'att_monthly_sum_emp_m_y_idx'); // Moi nhan vien chi co mot tong hop cho moi thang.
        });

        // Bang ca lam viec.
        Schema::create('work_shifts', function (Blueprint $table) {
            $table->id(); // Khoa chinh ca lam.
            $table->string('shift_name'); // Ten ca lam viec.
            $table->time('start_time'); // Gio bat dau ca.
            $table->time('end_time'); // Gio ket thuc ca.
            $table->unsignedInteger('standard_minutes'); // So phut lam viec chuan cua ca.
            $table->unsignedInteger('grace_minutes')->default(0); // So phut cho phep tre/som ma van tinh dung gio.
            $table->boolean('is_active')->default(true); // Ca lam con dang su dung hay khong.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang ngay nghi le/nghi toan cong ty.
        Schema::create('holidays', function (Blueprint $table) {
            $table->id(); // Khoa chinh ngay nghi.
            $table->date('holiday_date')->unique(); // Ngay nghi duy nhat.
            $table->string('holiday_name'); // Ten ngay nghi.
            $table->boolean('is_paid_leave')->default(true); // Co tinh luong hay khong.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang lich su tien do du an.
        Schema::create('project_progress_histories', function (Blueprint $table) {
            $table->id(); // Khoa chinh lich su.
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete(); // Du an duoc cap nhat tien do.
            $table->unsignedTinyInteger('old_progress')->default(0); // Phan tram tien do cu.
            $table->unsignedTinyInteger('new_progress')->default(0); // Phan tram tien do moi.
            $table->timestamp('changed_at'); // Thoi diem thay doi.
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi cap nhat tien do.
            $table->text('note')->nullable(); // Ghi chu thay doi tien do.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang log thay doi chi tiet cong viec trong du an.
        Schema::create('project_detail_logs', function (Blueprint $table) {
            $table->id(); // Khoa chinh log.
            $table->foreignId('implementation_detail_id')->constrained('project_implementation_details')->cascadeOnDelete(); // Chi tiet cong viec bi thay doi.
            $table->string('field_name'); // Ten truong du lieu da thay doi.
            $table->text('old_value')->nullable(); // Gia tri truoc khi sua.
            $table->text('new_value')->nullable(); // Gia tri sau khi sua.
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi thuc hien cap nhat.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang log gui email.
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id(); // Khoa chinh log email.
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete(); // User gui email neu co.
            $table->string('receiver_email'); // Email nguoi nhan.
            $table->string('subject'); // Tieu de email.
            $table->text('body_summary')->nullable(); // Tom tat noi dung email.
            $table->timestamp('sent_at')->useCurrent(); // Thoi diem gui.
            $table->string('status'); // Trang thai gui: success, failed...
            $table->text('error_message')->nullable(); // Loi tra ve neu gui that bai.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang lich su export du lieu.
        Schema::create('export_histories', function (Blueprint $table) {
            $table->id(); // Khoa chinh lich su export.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Nguoi thuc hien export.
            $table->string('module'); // Module du lieu duoc export.
            $table->string('file_type', 10); // Dinh dang file: excel, pdf...
            $table->json('filter_data')->nullable(); // Bo loc du lieu duoc ap dung khi export.
            $table->string('file_path')->nullable(); // Duong dan file da sinh ra.
            $table->timestamp('exported_at'); // Thoi diem export.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang log hoat dong chung trong he thong.
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id(); // Khoa chinh log hoat dong.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Nguoi thuc hien thao tac.
            $table->string('module'); // Module bi tac dong.
            $table->string('action'); // Hanh dong: create, update, delete, review...
            $table->text('description')->nullable(); // Mo ta chi tiet thao tac.
            $table->string('reference_table')->nullable(); // Bang du lieu lien quan.
            $table->unsignedBigInteger('reference_id')->nullable(); // ID ban ghi lien quan.
            $table->ipAddress('ip_address')->nullable(); // IP phat sinh thao tac.
            $table->timestamps(); // created_at, updated_at.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('export_histories');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('project_detail_logs');
        Schema::dropIfExists('project_progress_histories');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('work_shifts');
        Schema::dropIfExists('attendance_monthly_summaries');
        Schema::dropIfExists('attendance_approvals');
        Schema::dropIfExists('salary_histories');
        Schema::dropIfExists('employee_position_histories');
        Schema::dropIfExists('employee_department_histories');
        Schema::dropIfExists('employee_status_logs');
        Schema::dropIfExists('login_histories');
    }
};
