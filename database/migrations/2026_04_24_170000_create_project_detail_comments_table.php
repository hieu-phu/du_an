<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_detail_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('implementation_detail_id')->constrained('project_implementation_details')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->index(['implementation_detail_id', 'id'], 'pdc_detail_id_idx');
            $table->index(['project_id', 'implementation_detail_id'], 'pdc_project_detail_idx');
            $table->index('user_id', 'pdc_user_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_detail_comments');
    }
};
