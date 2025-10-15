<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syllabus_id')->constrained()->onDelete('cascade');
            $table->foreignId('quality_checklist_id')->constrained()->onDelete('cascade');
            $table->foreignId('checked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('checked_at')->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->enum('status', ['in_progress', 'completed', 'passed', 'requires_improvement', 'failed'])->default('in_progress');
            $table->json('item_results')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('auto_generated')->default(false);
            $table->timestamps();

            $table->unique(['syllabus_id', 'quality_checklist_id']);
            $table->index(['status']);
            $table->index(['checked_at']);
            $table->index(['auto_generated']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_quality_checks');
    }
};
