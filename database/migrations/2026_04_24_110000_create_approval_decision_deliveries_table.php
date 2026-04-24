<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_decision_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('dedupe_key', 190)->unique();
            $table->string('module', 50);
            $table->string('channel', 20)->default('mail');
            $table->string('decision', 20);
            $table->string('status', 20)->default('queued');
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_email');
            $table->string('subject');
            $table->string('action_url', 500)->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('payload')->nullable();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['module', 'status']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['recipient_email', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_decision_deliveries');
    }
};
