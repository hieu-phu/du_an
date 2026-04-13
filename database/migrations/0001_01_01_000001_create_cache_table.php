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
        // Bang cache key-value cua Laravel.
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary(); // Khoa cache duy nhat.
            $table->mediumText('value'); // Gia tri cache da duoc serialize.
            $table->integer('expiration'); // Moc het han dang unix timestamp.
        });

        // Bang lock cho cache.
        // Dung de tranh tranh chap khi xu ly cac tac vu can khoa.
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary(); // Ten khoa lock.
            $table->string('owner'); // Dinh danh tien trinh/worker dang so huu lock.
            $table->integer('expiration'); // Moc het han cua lock.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
