<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Danh dau user nay co phai nhan vien noi bo hay khong.
        Schema::table('users', function (Blueprint $table) {
            // true: co ho so nhan vien va tham gia nghiep vu HRM, false: tai khoan he thong/khach.
            $table->boolean('is_employee')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        // Hoan tac truong danh dau nhan vien.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_employee');
        });
    }
};
