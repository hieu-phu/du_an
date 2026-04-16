<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bo sung cap bac tham quyen cho tung chuc vu.
        Schema::table('positions', function (Blueprint $table) {
            // Muc do tham quyen de phan cap duyet/chuc nang (so lon hon = quyen cao hon).
            $table->unsignedTinyInteger('authority_level')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        // Hoan tac bo sung cap bac tham quyen.
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('authority_level');
        });
    }
};
