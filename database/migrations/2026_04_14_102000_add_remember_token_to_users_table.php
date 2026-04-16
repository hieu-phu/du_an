<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dam bao bang users co truong remember_token cho tinh nang "ghi nho dang nhap".
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'remember_token')) {
                // Token luu cookie dang nhap dai han.
                $table->rememberToken()->nullable()->after('password');
            }
        });
    }

    public function down(): void
    {
        // Hoan tac truong remember_token neu migration da tao.
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
