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
        // Bang queue jobs cua Laravel.
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // Khoa chinh cua job.
            $table->string('queue')->index(); // Ten hang doi xu ly.
            $table->longText('payload'); // Noi dung job da duoc serialize.
            $table->unsignedTinyInteger('attempts'); // So lan worker da thu chay job.
            $table->unsignedInteger('reserved_at')->nullable(); // Thoi diem worker nhan job.
            $table->unsignedInteger('available_at'); // Thoi diem job san sang duoc xu ly.
            $table->unsignedInteger('created_at'); // Thoi diem tao job.
        });

        // Bang theo doi mot lo job duoc dispatch theo batch.
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary(); // Ma batch duy nhat.
            $table->string('name'); // Ten batch de phan biet.
            $table->integer('total_jobs'); // Tong so job trong batch.
            $table->integer('pending_jobs'); // So job con dang cho xu ly.
            $table->integer('failed_jobs'); // So job that bai.
            $table->longText('failed_job_ids'); // Danh sach ID job that bai.
            $table->mediumText('options')->nullable(); // Tuy chon cau hinh batch.
            $table->integer('cancelled_at')->nullable(); // Thoi diem huy batch.
            $table->integer('created_at'); // Thoi diem tao batch.
            $table->integer('finished_at')->nullable(); // Thoi diem hoan tat batch.
        });

        // Bang log job loi de debug va retry.
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id(); // Khoa chinh ban ghi loi.
            $table->string('uuid')->unique(); // UUID duy nhat cua job that bai.
            $table->text('connection'); // Ket noi queue da su dung.
            $table->text('queue'); // Ten queue da chay.
            $table->longText('payload'); // Du lieu job gay loi.
            $table->longText('exception'); // Stack trace va thong tin exception.
            $table->timestamp('failed_at')->useCurrent(); // Thoi diem xay ra loi.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
