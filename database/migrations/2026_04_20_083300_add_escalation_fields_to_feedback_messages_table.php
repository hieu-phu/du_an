<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_messages', function (Blueprint $table): void {
            if (!Schema::hasColumn('feedback_messages', 'last_escalated_at')) {
                $table->timestamp('last_escalated_at')->nullable()->after('replied_at');
            }

            if (!Schema::hasColumn('feedback_messages', 'escalation_count')) {
                $table->unsignedSmallInteger('escalation_count')->default(0)->after('last_escalated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('feedback_messages', function (Blueprint $table): void {
            if (Schema::hasColumn('feedback_messages', 'escalation_count')) {
                $table->dropColumn('escalation_count');
            }

            if (Schema::hasColumn('feedback_messages', 'last_escalated_at')) {
                $table->dropColumn('last_escalated_at');
            }
        });
    }
};
