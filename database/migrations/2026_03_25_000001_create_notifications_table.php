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
        // Bang thong bao cua he thong gui toi nguoi dung.
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // Khoa chinh cua thong bao.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('Nguoi nhan thong bao.');
            $table->string('title')->comment('Tieu de ngan de hien thi trong danh sach thong bao.');
            $table->text('message')->comment('Noi dung chi tiet cua thong bao.');
            $table->json('data')->nullable()->comment('Du lieu bo sung dang JSON de client xu ly.');
            $table->string('url_link')->nullable()->comment('Lien ket dieu huong khi nguoi dung bam vao thong bao.');
            $table->string('subdomain')->nullable()->comment('Subdomain lien quan neu he thong tach theo tenant.');
            $table->string('category')->default('general')->comment('Nhom thong bao: general, hrm, project...');
            $table->timestamp('read_at')->nullable()->comment('Thoi diem nguoi dung da doc thong bao.');
            $table->bigInteger('creater_id')->nullable()->comment('ID nguoi tao ra thong bao.');
            $table->string('reference_type')->nullable()->comment('Loai doi tuong goc ma thong bao dang tham chieu.');
            $table->bigInteger('reference_id')->nullable()->comment('ID doi tuong goc ma thong bao dang tham chieu.');
            $table->timestamps(); // created_at, updated_at.
            $table->index('user_id'); // Toi uu truy van thong bao theo nguoi dung.
            $table->index('category'); // Toi uu loc thong bao theo nhom.
            $table->comment('Bang luu thong bao he thong gui den nguoi dung.');
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
