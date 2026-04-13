<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bang tai khoan trung tam cua he thong.
        // Luu thong tin dang nhap, thong tin lien he va trang thai nguoi dung.
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Khoa chinh cua tai khoan.
            $table->string('name'); // Ho ten hien thi cua nguoi dung.
            $table->string('email')->unique(); // Email dang nhap, bat buoc duy nhat.
            $table->string('username')->unique(); // Ten dang nhap dang text, duy nhat.
            $table->string('phone', 15)->nullable()->unique(); // So dien thoai, co the de trong nhung neu co thi phai duy nhat.
            $table->string('password'); // Mat khau da duoc bam hash.

            $table->text('address')->nullable(); // Dia chi hien tai cua nguoi dung.
            $table->text('avatar')->nullable(); // Duong dan hoac URL anh dai dien chinh.
            $table->bigInteger('creater_id')->nullable(); // ID nguoi tao tai khoan nay.
            $table->enum('status', ['active', 'inactive', 'blocked', 'pending'])->default('pending')
                ->comment('Trang thai tai khoan: active, inactive, blocked, pending.');
            $table->timestamp('email_verified_at')->nullable(); // Thoi diem email duoc xac minh.
            $table->boolean('zalo_verified')->default(false); // Danh dau tai khoan da lien ket/xac minh Zalo hay chua.
            $table->timestamp('zalo_verified_at')->nullable(); // Thoi diem xac minh Zalo.
            $table->string('zalo_user_id')->nullable(); // ID nguoi dung ben he thong Zalo de doi soat.
            $table->timestamp('last_login_at')->nullable(); // Lan dang nhap gan nhat.
            $table->string('last_login_ip', 45)->nullable(); // IP cua lan dang nhap gan nhat.
            $table->timestamps(); // created_at, updated_at.

            $table->comment('Bang luu thong tin tai khoan nguoi dung va khach hang.');
        });

        // Bang lien ket tai khoan noi bo voi tai khoan dang nhap qua ben thu ba.
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id(); // Khoa chinh cua ban ghi lien ket.
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Tai khoan noi bo so huu lien ket social.
            $table->string('provider'); // Nha cung cap dang nhap: google, facebook, github...
            $table->string('provider_id'); // ID nguoi dung tai nha cung cap.
            $table->string('provider_email')->nullable(); // Email lay ve tu nha cung cap.
            $table->string('avatar')->nullable(); // Anh dai dien tu nha cung cap.
            $table->timestamps(); // created_at, updated_at.
            $table->unique(['provider', 'provider_id']); // Khong cho trung mot tai khoan social.
        });

        // Bang luu token dat lai mat khau.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email yeu cau reset, dong vai tro khoa chinh.
            $table->string('token'); // Token dung de xac thuc thao tac reset mat khau.
            $table->string('ip_address', 45)->nullable(); // IP gui yeu cau reset.
            $table->string('user_agent')->nullable(); // Trinh duyet/thiet bi gui yeu cau reset.
            $table->timestamp('created_at')->nullable()->comment('Thoi diem tao token reset.');
        });

        // Bang luu session dang nhap.
        // Dung khi he thong quan ly phien dang nhap bang database.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Ma session duy nhat.
            $table->foreignId('user_id')->nullable()->index(); // Tai khoan so huu session, co the rong voi khach.
            $table->string('ip_address', 45)->nullable()->comment('IP dang nhap cua session.');
            $table->text('user_agent')->nullable()->comment('Thong tin trinh duyet/thiet bi.');
            $table->longText('payload')->comment('Du lieu session da duoc serialize.');
            $table->timestamp('login_at')->nullable()->comment('Thoi diem bat dau dang nhap.');
            $table->time('logout_at')->nullable()->comment('Thoi diem dang xuat neu co.');
            $table->string('device_name')->nullable()->comment('Ten thiet bi neu ung dung co gui len.');
            $table->enum('session_type', ['web', 'mobile', 'api'])->default('web')->comment('Loai phien dang nhap.');
            $table->integer('last_activity')->index()->comment('Moc thoi gian hoat dong cuoi dang unix timestamp.');
            $table->comment('Bang luu phien dang nhap cua nguoi dung.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
