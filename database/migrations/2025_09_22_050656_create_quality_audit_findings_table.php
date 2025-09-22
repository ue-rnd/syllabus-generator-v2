<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_audit_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_audit_id')->constrained()->onDelete('cascade');
            $table->foreignId('syllabus_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['critical', 'high', 'medium', 'low', 'info']);
            $table->enum('category', ['content', 'structure', 'compliance', 'quality', 'documentation', 'process']);
            $table->json('evidence')->nullable();
            $table->text('recommendation')->nullable();
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['quality_audit_id', 'status']);
            $table->index(['syllabus_id', 'status']);
            $table->index(['severity', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_audit_findings');
    }
};
