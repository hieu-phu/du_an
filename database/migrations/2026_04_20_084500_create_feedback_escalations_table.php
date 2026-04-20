<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_escalations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('feedback_message_id')->constrained('feedback_messages')->cascadeOnDelete();
            $table->foreignId('from_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('to_position_id')->constrained('positions')->cascadeOnDelete();
            $table->unsignedSmallInteger('escalation_count')->default(1);
            $table->timestamp('escalated_at');
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->index(['feedback_message_id', 'escalated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_escalations');
    }
};
