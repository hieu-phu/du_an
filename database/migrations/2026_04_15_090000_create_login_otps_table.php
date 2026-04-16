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
        // Bang OTP dung cho dang nhap 2 buoc/kiem tra bo sung.
        Schema::create('login_otps', function (Blueprint $table) {
            $table->id(); // Khoa chinh ban ghi OTP.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // User nhan OTP dang nhap.
            $table->string('otp'); // Ma OTP xac thuc dang nhap.
            $table->timestamp('expires_at'); // Han hieu luc OTP.
            $table->timestamps(); // created_at, updated_at.

            $table->comment('Bang luu OTP xac thuc dang nhap cho nguoi dung.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_otps');
    }
};
