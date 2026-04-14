<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            // Danh sách quyền hạn nghiệp vụ của chức vụ (JSON array of capability keys).
            $table->json('capabilities')->nullable()->after('authority_level')->comment('Quyền hạn nghiệp vụ của chức vụ.');
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('capabilities');
        });
    }
};
