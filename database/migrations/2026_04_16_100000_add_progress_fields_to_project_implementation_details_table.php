<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bo sung cot theo doi tien do chi tiet va trang thai khoa cong viec.
        Schema::table('project_implementation_details', function (Blueprint $table): void {
            if (!Schema::hasColumn('project_implementation_details', 'progress_percent')) {
                // Phan tram hoan thanh cua dau viec (0-100).
                $table->unsignedTinyInteger('progress_percent')->default(0)->after('detail_status');
            }

            if (!Schema::hasColumn('project_implementation_details', 'actual_end_date')) {
                // Ngay hoan thanh thuc te cua dau viec.
                $table->date('actual_end_date')->nullable()->after('expected_end_date');
            }

            if (!Schema::hasColumn('project_implementation_details', 'is_locked')) {
                // Danh dau dau viec da chot, khong cho sua tiep.
                $table->boolean('is_locked')->default(false)->after('progress_percent');
            }
        });
    }
    public function down(): void
    {
        // Hoan tac cac cot tien do bo sung.
        Schema::table('project_implementation_details', function (Blueprint $table): void {
            if (Schema::hasColumn('project_implementation_details', 'is_locked')) {
                $table->dropColumn('is_locked');
            }

            if (Schema::hasColumn('project_implementation_details', 'actual_end_date')) {
                $table->dropColumn('actual_end_date');
            }

            if (Schema::hasColumn('project_implementation_details', 'progress_percent')) {
                $table->dropColumn('progress_percent');
            }
        });
    }
};
