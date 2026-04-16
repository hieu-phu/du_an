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
        // Bo sung thong tin truy vet de audit chi tiet hon.
        Schema::table('activity_logs', function (Blueprint $table) {
            // Ten thiet bi may khach neu he thong thu thap duoc.
            $table->string('device')->nullable()->after('ip_address');
            // Raw user-agent de phan tich trinh duyet/OS.
            $table->text('user_agent')->nullable()->after('device');
            // Thoi diem thuc te xay ra su kien (tach voi created_at khi can dong bo).
            $table->timestamp('occurred_at')->nullable()->after('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hoan tac cac truong truy vet bo sung.
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['device', 'user_agent', 'occurred_at']);
        });
    }
};
