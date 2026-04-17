<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_messages', function (Blueprint $table): void {
            if (!Schema::hasColumn('feedback_messages', 'receiver_position_id')) {
                $table->foreignId('receiver_position_id')
                    ->nullable()
                    ->after('receiver_id')
                    ->constrained('positions')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('feedback_messages', function (Blueprint $table): void {
            if (Schema::hasColumn('feedback_messages', 'receiver_position_id')) {
                $table->dropConstrainedForeignId('receiver_position_id');
            }
        });
    }
};
