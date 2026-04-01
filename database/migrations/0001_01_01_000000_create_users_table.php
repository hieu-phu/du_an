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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('phone', 15)->nullable()->unique();
            $table->string('password');

            $table->text('address')->nullable();
            $table->text('avatar')->nullable();
            $table->bigInteger('creater_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked', 'pending'])->default('pending')
                ->comment("active: Đang hoạt động, inactive: Không hoạt động, blocked: Bị khóa, pending: Chờ duyệt");
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('zalo_verified')->default(false);
            $table->timestamp('zalo_verified_at')->nullable();
            $table->string('zalo_user_id')->nullable();      // cỏ thể cần cho sau này
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamps();

            $table->comment("bảng lưu thông tin tài khoản của khách hàng");
        });

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider');        // google, facebook, github...
            $table->string('provider_id');     // ID trên hệ thống social
            $table->string('provider_email')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'provider_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable()->comment("Thời gian tạo token");
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->comment("Địa chỉ IP đăng nhập");
            $table->text('user_agent')->nullable()->comment("User Agent thiết bị đăng nhập");
            $table->longText('payload')->comment('lưu thông tin token đăng nhập');
            $table->timestamp('login_at')->nullable()->comment("Thời gian đăng nhập");
            $table->time('logout_at')->nullable()->comment("Thời gian đăng xuất");
            $table->string('device_name')->nullable()->comment("Tên thiết bị đăng nhập");
            $table->enum('session_type', ['web', 'mobile', 'api'])->default('web')->comment("Loại phiên đăng nhập");
            $table->integer('last_activity')->index()->comment("Thời gian hoạt động cuối cùng");
            $table->comment('Bảng lưu thông tin đăng nhập');
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
