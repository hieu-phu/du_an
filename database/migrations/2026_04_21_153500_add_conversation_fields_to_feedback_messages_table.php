<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_messages', function (Blueprint $table): void {
            if (!Schema::hasColumn('feedback_messages', 'conversation_status')) {
                $table->string('conversation_status', 30)->default('waiting_handler')->after('status');
            }

            if (!Schema::hasColumn('feedback_messages', 'waiting_for')) {
                $table->string('waiting_for', 20)->nullable()->after('conversation_status');
            }

            if (!Schema::hasColumn('feedback_messages', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('replied_at');
            }

            if (!Schema::hasColumn('feedback_messages', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('resolved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('feedback_messages', function (Blueprint $table): void {
            if (Schema::hasColumn('feedback_messages', 'closed_at')) {
                $table->dropColumn('closed_at');
            }

            if (Schema::hasColumn('feedback_messages', 'resolved_at')) {
                $table->dropColumn('resolved_at');
            }

            if (Schema::hasColumn('feedback_messages', 'waiting_for')) {
                $table->dropColumn('waiting_for');
            }

            if (Schema::hasColumn('feedback_messages', 'conversation_status')) {
                $table->dropColumn('conversation_status');
            }
        });
    }
};
