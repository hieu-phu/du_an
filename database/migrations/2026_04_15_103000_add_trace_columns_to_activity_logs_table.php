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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('device')->nullable()->after('ip_address');
            $table->text('user_agent')->nullable()->after('device');
            $table->timestamp('occurred_at')->nullable()->after('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['device', 'user_agent', 'occurred_at']);
        });
    }
};
