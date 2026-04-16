<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bang luu lich su phan hoi nhieu lan cho moi feedback.
        Schema::create('feedback_replies', function (Blueprint $table) {
            $table->id(); // Khoa chinh ban ghi reply.
            $table->foreignId('feedback_message_id')->constrained('feedback_messages')->cascadeOnDelete(); // Feedback goc duoc phan hoi.
            $table->foreignId('replied_by')->constrained('users')->cascadeOnDelete(); // Nguoi phan hoi.
            $table->text('message'); // Noi dung phan hoi.
            $table->timestamps(); // created_at, updated_at.

            // Toi uu tai lich su phan hoi theo feedback theo thu tu thoi gian.
            $table->index(['feedback_message_id', 'created_at']);
            $table->comment('Bang luu cac phan hoi theo chuoi hoi-thoai feedback.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_replies');
    }
};
