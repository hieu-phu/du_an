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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment("Người nhận thông báo");
            $table->string('title')->comment("Tiêu đề thông báo");
            $table->text('message')->comment("Nội dung thông báo");
            $table->json('data')->nullable()->comment("Dữ liệu đính kèm");
            $table->string('url_link')->nullable()->comment("Liên kết đến thông báo");
            $table->string('subdomain')->nullable()->comment("Subdomain của thông báo");
            $table->string('category')->default('general')->comment("Danh mục thông báo");
            $table->timestamp('read_at')->nullable()->comment("Thời gian đọc thông báo");
            $table->bigInteger('creater_id')->nullable()->comment("Người tạo thông báo");
            $table->string('reference_type')->nullable()->comment("Loại tham chiếu");
            $table->bigInteger('reference_id')->nullable()->comment("ID tham chiếu");
            $table->timestamps();
            $table->index('user_id');
            $table->index('category');
            $table->comment('Bảng lưu thông báo của hệ thống');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
