<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bo sung danh sach capability nghiep vu cho tung chuc vu (luu tam thoi dang JSON).
        Schema::table('positions', function (Blueprint $table) {
            // Mang JSON cac capability code duoc gan cho chuc vu.
            $table->json('capabilities')->nullable()->after('authority_level')->comment('Danh sach capability code cua chuc vu.');
        });
    }

    public function down(): void
    {
        // Hoan tac cot capabilities bo sung.
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('capabilities');
        });
    }
};
