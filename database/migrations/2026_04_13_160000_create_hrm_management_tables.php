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
        // Bang danh muc tinh/thanh pho.
        Schema::create('provinces', function (Blueprint $table) {
            $table->id(); // Khoa chinh tinh/thanh.
            $table->string('code', 20)->nullable()->unique(); // Ma hanh chinh cua tinh/thanh.
            $table->string('name'); // Ten tinh/thanh pho.
            $table->string('type', 50)->nullable(); // Loai don vi hanh chinh.
            $table->boolean('is_active')->default(true); // Danh dau ban ghi con duoc su dung hay khong.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang danh muc quan/huyen thuoc tinh.
        Schema::create('districts', function (Blueprint $table) {
            $table->id(); // Khoa chinh quan/huyen.
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete(); // Tinh/thanh ma quan/huyen nay thuoc ve.
            $table->string('code', 20)->unique(); // Ma hanh chinh cua quan/huyen.
            $table->string('name'); // Ten quan/huyen.
            $table->string('type', 50)->nullable(); // Loai don vi hanh chinh.
            $table->boolean('is_active')->default(true); // Danh dau con su dung hay khong.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['province_id', 'name']); // Toi uu tim quan/huyen theo tinh va ten.
        });

        // Bang danh muc phuong/xa.
        Schema::create('wards', function (Blueprint $table) {
            $table->id(); // Khoa chinh phuong/xa.
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete(); // Tinh/thanh cua phuong/xa.
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete(); // Quan/huyen cua phuong/xa.
            $table->string('code', 20)->nullable()->unique(); // Ma hanh chinh cua phuong/xa.
            $table->string('name'); // Ten phuong/xa.
            $table->string('type', 50)->nullable(); // Loai don vi hanh chinh.
            $table->boolean('is_active')->default(true); // Danh dau con su dung hay khong.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['province_id', 'name']); // Toi uu tim theo tinh.
            $table->index(['district_id', 'name']); // Toi uu tim theo quan/huyen.
        });

        // Bang phong ban trong cong ty.
        Schema::create('departments', function (Blueprint $table) {
            $table->id(); // Khoa chinh phong ban.
            $table->string('name')->unique(); // Ten phong ban.
            $table->text('description')->nullable(); // Mo ta chuc nang nhiem vu cua phong ban.
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete(); // User dang giu vai tro truong phong.
            $table->boolean('is_active')->default(true); // Danh dau phong ban dang hoat dong hay da ngung.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang chuc vu.
        Schema::create('positions', function (Blueprint $table) {
            $table->id(); // Khoa chinh chuc vu.
            $table->string('name')->unique(); // Ten chuc vu.
            $table->text('description')->nullable(); // Mo ta vai tro cong viec.
            $table->boolean('is_active')->default(true); // Danh dau chuc vu con ap dung hay khong.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang ho so nhan vien chi tiet.
        // Day la bang HRM trung tam noi user voi thong tin nhan su.
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id(); // Khoa chinh ho so nhan vien.
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete(); // Tai khoan he thong cua nhan vien.
            $table->string('employee_code', 50)->unique(); // Ma nhan vien duy nhat de tham chieu nghiep vu.
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete(); // Phong ban hien tai.
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete(); // Chuc vu hien tai.
            $table->foreignId('reports_to_user_id')->nullable()->constrained('users')->nullOnDelete(); // Quan ly truc tiep cua nhan vien.
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete(); // Tinh/thanh cua dia chi.
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete(); // Quan/huyen cua dia chi.
            $table->foreignId('ward_id')->nullable()->constrained('wards')->nullOnDelete(); // Phuong/xa cua dia chi.
            $table->string('address_line', 255)->nullable(); // Dia chi chi tiet: so nha, duong...
            $table->date('date_of_birth')->nullable(); // Ngay sinh.
            $table->date('hire_date'); // Ngay bat dau lam viec.
            $table->decimal('base_salary', 15, 2)->default(0); // Muc luong co ban hien tai.
            $table->string('salary_currency', 10)->default('VND'); // Don vi tien te cua luong.
            $table->enum('employment_status', ['active', 'inactive', 'probation', 'on_leave', 'terminated'])->default('active'); // Trang thai lam viec.
            $table->boolean('is_department_head')->default(false); // Danh dau nhan vien co phai truong phong hay khong.
            $table->timestamp('locked_at')->nullable(); // Thoi diem ho so bi khoa chinh sua.
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi khoa ho so.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['department_id', 'employment_status']); // Toi uu loc nhan vien theo phong ban va trang thai.
            $table->index(['position_id', 'employment_status']); // Toi uu loc nhan vien theo chuc vu va trang thai.
            $table->index('hire_date'); // Toi uu truy van theo ngay vao lam.
        });

        // Bang cham cong tong hop theo ngay.
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id(); // Khoa chinh bang cong ngay.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien duoc cham cong.
            $table->date('work_date'); // Ngay lam viec.
            $table->dateTime('check_in_at')->nullable(); // Gio vao dau tien ghi nhan trong ngay.
            $table->dateTime('check_out_at')->nullable(); // Gio ra cuoi cung ghi nhan trong ngay.
            $table->integer('worked_minutes')->default(0); // Tong so phut da lam viec trong ngay.
            $table->enum('attendance_status', ['pending', 'present', 'late', 'absent', 'half_day', 'leave'])->default('pending'); // Ket qua cham cong tong hop.
            $table->boolean('is_confirmed')->default(false); // Da duoc quan ly/HR xac nhan hay chua.
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi xac nhan cong.
            $table->timestamp('confirmed_at')->nullable(); // Thoi diem xac nhan.
            $table->text('note')->nullable(); // Ghi chu bo sung ve cong.
            $table->timestamps(); // created_at, updated_at.

            $table->unique(['employee_profile_id', 'work_date']); // Moi nhan vien chi co mot ban ghi tong hop moi ngay.
            $table->index(['work_date', 'attendance_status']); // Toi uu loc cong theo ngay va trang thai.
        });

        // Bang log tung su kien cham cong.
        Schema::create('attendance_events', function (Blueprint $table) {
            $table->id(); // Khoa chinh su kien.
            $table->foreignId('attendance_record_id')->constrained('attendance_records')->cascadeOnDelete(); // Ban ghi tong hop ma su kien thuoc ve.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien phat sinh su kien.
            $table->enum('event_type', ['check_in', 'check_out', 'manual_adjustment']); // Loai su kien cham cong.
            $table->dateTime('event_at'); // Thoi diem xay ra su kien.
            $table->string('source', 30)->default('web'); // Nguon ghi nhan: web, app, may cham cong...
            $table->string('ip_address', 45)->nullable(); // IP phat sinh su kien neu co.
            $table->text('note')->nullable(); // Ghi chu su kien.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi tao su kien thu cong.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['employee_profile_id', 'event_at']); // Toi uu tim lich su cham cong theo nhan vien.
        });

        // Bang du an.
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // Khoa chinh du an.
            $table->string('name'); // Ten du an.
            $table->date('start_date'); // Ngay bat dau du an.
            $table->enum('status', ['planning', 'in_progress', 'on_hold', 'completed'])->default('in_progress'); // Trang thai van hanh cua du an.
            $table->text('description')->nullable(); // Mo ta pham vi va muc tieu du an.
            $table->boolean('is_locked')->default(false); // Danh dau du an dang bi khoa chinh sua.
            $table->timestamp('locked_at')->nullable(); // Thoi diem khoa.
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi thuc hien khoa.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi tao du an.
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi cap nhat cuoi.
            $table->timestamps(); // created_at, updated_at.

            $table->unique(['name', 'start_date']); // Tranh trung du an cung ten va cung ngay bat dau.
            $table->index('status'); // Toi uu loc theo trang thai.
        });

        // Bang vai tro trong tung du an.
        Schema::create('project_roles', function (Blueprint $table) {
            $table->id(); // Khoa chinh vai tro du an.
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete(); // Du an so huu vai tro.
            $table->string('name'); // Ten vai tro trong du an.
            $table->text('description')->nullable(); // Mo ta trach nhiem cua vai tro.
            $table->timestamps(); // created_at, updated_at.

            $table->unique(['project_id', 'name']); // Moi ten vai tro chi duy nhat trong mot du an.
        });

        // Bang thanh vien tham gia du an.
        Schema::create('project_members', function (Blueprint $table) {
            $table->id(); // Khoa chinh ban ghi thanh vien.
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete(); // Du an ma nhan vien tham gia.
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete(); // Nhan vien tham gia.
            $table->foreignId('project_role_id')->nullable()->constrained('project_roles')->nullOnDelete(); // Vai tro cua nhan vien trong du an.
            $table->date('joined_at')->nullable(); // Ngay tham gia.
            $table->date('left_at')->nullable(); // Ngay roi du an neu co.
            $table->boolean('is_active')->default(true); // Thanh vien con dang active trong du an hay khong.
            $table->text('note')->nullable(); // Ghi chu them.
            $table->timestamps(); // created_at, updated_at.

            $table->unique(['project_id', 'employee_profile_id']); // Moi nhan vien chi co mot ban ghi thanh vien trong mot du an.
            $table->index(['project_id', 'is_active']); // Toi uu danh sach thanh vien dang hoat dong.
        });

        // Bang chi tiet cong viec/trien khai trong du an.
        Schema::create('project_implementation_details', function (Blueprint $table) {
            $table->id(); // Khoa chinh chi tiet cong viec.
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete(); // Du an ma cong viec thuoc ve.
            $table->foreignId('assigned_to')->nullable()->constrained('employee_profiles')->nullOnDelete(); // Nhan vien duoc giao phu trach.
            $table->text('content'); // Noi dung cong viec can thuc hien.
            $table->date('execution_date'); // Ngay bat dau/du kien thuc hien.
            $table->unsignedInteger('duration_days'); // So ngay du kien can de xu ly.
            $table->date('expected_end_date'); // Han hoan thanh du kien.
            $table->enum('detail_status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned'); // Trang thai cong viec.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi tao chi tiet cong viec.
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi cap nhat cuoi.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['project_id', 'detail_status']); // Toi uu loc cong viec theo du an va trang thai.
            $table->index(['assigned_to', 'execution_date']); // Toi uu lich cong viec cua nhan vien.
        });

        // Bang phieu de nghi phe duyet.
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id(); // Khoa chinh phieu de nghi.
            $table->string('request_type', 50); // Loai de nghi: leave, overtime, manual_attendance...
            $table->string('target_type', 100); // Ten model nghiep vu bi tac dong.
            $table->unsignedBigInteger('target_id'); // ID ban ghi nghiep vu bi tac dong.
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete(); // Nguoi gui de nghi.
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi da xem xet/duyet.
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending'); // Trang thai xu ly de nghi.
            $table->timestamp('submitted_at')->nullable(); // Thoi diem gui de nghi.
            $table->timestamp('reviewed_at')->nullable(); // Thoi diem duyet/tu choi.
            $table->text('reason')->nullable(); // Ly do hoac noi dung nguoi gui trinh bay.
            $table->text('review_note')->nullable(); // Ghi chu cua nguoi duyet.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['target_type', 'target_id']); // Toi uu truy vet de nghi theo doi tuong goc.
            $table->index(['status', 'request_type']); // Toi uu danh sach de nghi theo trang thai va loai.
        });

        // Bang chi tiet cac truong thay doi trong tung de nghi phe duyet.
        Schema::create('approval_request_changes', function (Blueprint $table) {
            $table->id(); // Khoa chinh thay doi de nghi.
            $table->foreignId('approval_request_id')->constrained('approval_requests')->cascadeOnDelete(); // De nghi cha.
            $table->string('field_name', 100); // Ten truong du lieu muon thay doi.
            $table->text('old_value')->nullable(); // Gia tri hien tai truoc khi thay doi.
            $table->text('new_value')->nullable(); // Gia tri moi duoc de xuat.
            $table->timestamps(); // created_at, updated_at.
        });

        // Bang tin nhan/gop y noi bo.
        Schema::create('feedback_messages', function (Blueprint $table) {
            $table->id(); // Khoa chinh tin nhan.
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete(); // Nguoi gui.
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete(); // Nguoi nhan cu the neu gui dich danh.
            $table->enum('receiver_group', ['admin', 'hr', 'specific_user'])->default('hr'); // Nhom doi tuong nhan.
            $table->string('subject'); // Tieu de tin nhan.
            $table->text('message'); // Noi dung gop y/phan hoi.
            $table->enum('status', ['sent', 'read', 'archived'])->default('sent'); // Trang thai xu ly tin nhan.
            $table->timestamp('read_at')->nullable(); // Thoi diem nguoi nhan da doc.
            $table->timestamps(); // created_at, updated_at.

            $table->index(['receiver_group', 'status']); // Toi uu hop thu theo nhom va trang thai.
        });

        // Bang cau hinh chung cua website/he thong.
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id(); // Khoa chinh cau hinh.
            $table->string('site_name')->default('HRM'); // Ten he thong hien thi.
            $table->string('logo_path', 255)->nullable(); // Duong dan file logo.
            $table->string('favicon_path', 255)->nullable(); // Duong dan file favicon.
            $table->text('header_content')->nullable(); // Noi dung tuy bien khu vuc header.
            $table->text('footer_content')->nullable(); // Noi dung tuy bien khu vuc footer.
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi cap nhat cau hinh cuoi.
            $table->timestamps(); // created_at, updated_at.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('feedback_messages');
        Schema::dropIfExists('approval_request_changes');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('project_implementation_details');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('project_roles');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('attendance_events');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('employee_profiles');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }
};
