<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bo sung thong tin phan hoi truc tiep tren feedback goc.
        Schema::table('feedback_messages', function (Blueprint $table): void {
            if (!Schema::hasColumn('feedback_messages', 'is_replied')) {
                // Danh dau feedback da co nguoi phan hoi hay chua.
                $table->boolean('is_replied')->default(false)->after('read_at');
            }

            if (!Schema::hasColumn('feedback_messages', 'reply_message')) {
                // Noi dung phan hoi rut gon (su dung cho case 1 lan reply).
                $table->text('reply_message')->nullable()->after('is_replied');
            }

            if (!Schema::hasColumn('feedback_messages', 'replied_by')) {
                // User thuc hien phan hoi.
                $table->foreignId('replied_by')->nullable()->after('reply_message')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('feedback_messages', 'replied_at')) {
                // Thoi diem feedback duoc phan hoi.
                $table->timestamp('replied_at')->nullable()->after('replied_by');
            }
        });
    }

    public function down(): void
    {
        // Hoan tac cac cot phan hoi bo sung.
        Schema::table('feedback_messages', function (Blueprint $table): void {
            if (Schema::hasColumn('feedback_messages', 'replied_at')) {
                $table->dropColumn('replied_at');
            }

            if (Schema::hasColumn('feedback_messages', 'replied_by')) {
                $table->dropConstrainedForeignId('replied_by');
            }

            if (Schema::hasColumn('feedback_messages', 'reply_message')) {
                $table->dropColumn('reply_message');
            }

            if (Schema::hasColumn('feedback_messages', 'is_replied')) {
                $table->dropColumn('is_replied');
            }
        });
    }
};
