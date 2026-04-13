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
        // Bo sung them truong hinh anh thu nho va slug cho bang users.
        Schema::table('users', function (Blueprint $table) {
            $table->text('thumbnail')->nullable()->after('avatar')->comment('Anh thumbnail nho dung cho danh sach hoac preview.');
            $table->string('slug')->nullable()->unique()->after('thumbnail')->comment('Slug than thien de tao URL ho so.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'slug']);
        });
    }
};
