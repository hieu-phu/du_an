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
        // Bang OTP phuc vu quy trinh quen mat khau qua email.
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id(); // Khoa chinh ban ghi OTP.
            $table->string('email')->index(); // Email yeu cau dat lai mat khau.
            $table->string('otp'); // Ma OTP gui toi nguoi dung de xac thuc.
            $table->timestamp('expires_at'); // Han hieu luc cua OTP.
            $table->timestamps(); // created_at, updated_at.

            $table->comment('Bang luu OTP dat lai mat khau theo email.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};
