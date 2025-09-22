<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_audit_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_audit_id')->constrained()->onDelete('cascade');
            $table->foreignId('quality_audit_finding_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('action_type', ['corrective', 'preventive', 'improvement', 'training', 'documentation', 'process_change']);
            $table->enum('priority', ['critical', 'high', 'medium', 'low']);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'on_hold'])->default('pending');
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('evidence')->nullable();
            $table->timestamps();

            $table->index(['quality_audit_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_audit_actions');
    }
};
