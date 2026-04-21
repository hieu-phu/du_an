<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->boolean('is_paid')->default(true);
            $table->boolean('deducts_balance')->default(true);
            $table->boolean('requires_attachment')->default(false);
            $table->decimal('annual_quota', 8, 2)->default(0);
            $table->decimal('max_days_per_request', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->decimal('opening_balance', 8, 2)->default(0);
            $table->decimal('accrued_days', 8, 2)->default(0);
            $table->decimal('used_days', 8, 2)->default(0);
            $table->decimal('pending_days', 8, 2)->default(0);
            $table->decimal('adjusted_days', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['employee_profile_id', 'leave_type_id', 'year'], 'employee_leave_balances_unique_year_type');
        });

        Schema::create('leave_balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_leave_balance_id')->constrained('employee_leave_balances')->cascadeOnDelete();
            $table->foreignId('attendance_request_id')->nullable()->constrained('attendance_requests')->nullOnDelete();
            $table->string('type', 30);
            $table->decimal('days', 8, 2);
            $table->decimal('balance_after', 8, 2)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'created_at'], 'leave_balance_transactions_type_created_idx');
        });

        Schema::table('attendance_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_requests', 'leave_type_id')) {
                $table->foreignId('leave_type_id')->nullable()->after('request_type')->constrained('leave_types')->nullOnDelete();
            }

            if (!Schema::hasColumn('attendance_requests', 'leave_days')) {
                $table->decimal('leave_days', 8, 2)->default(0)->after('leave_type');
            }
        });

        DB::table('leave_types')->insert([
            [
                'code' => 'ANNUAL',
                'name' => 'Nghi phep nam',
                'is_paid' => true,
                'deducts_balance' => true,
                'requires_attachment' => false,
                'annual_quota' => 12,
                'description' => 'Phep nam co luong, tru vao quy phep.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SICK',
                'name' => 'Nghi om',
                'is_paid' => true,
                'deducts_balance' => true,
                'requires_attachment' => true,
                'annual_quota' => 6,
                'description' => 'Nghi om co luong, co the yeu cau minh chung.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UNPAID',
                'name' => 'Nghi khong luong',
                'is_paid' => false,
                'deducts_balance' => false,
                'requires_attachment' => false,
                'annual_quota' => 0,
                'description' => 'Nghi khong luong, khong tru quy phep va khong tinh cong.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_requests', 'leave_type_id')) {
                $table->dropConstrainedForeignId('leave_type_id');
            }

            if (Schema::hasColumn('attendance_requests', 'leave_days')) {
                $table->dropColumn('leave_days');
            }
        });

        Schema::dropIfExists('leave_balance_transactions');
        Schema::dropIfExists('employee_leave_balances');
        Schema::dropIfExists('leave_types');
    }
};
