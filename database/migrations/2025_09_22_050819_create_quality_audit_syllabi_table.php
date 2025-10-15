<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_audit_syllabi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_audit_id')->constrained()->onDelete('cascade');
            $table->foreignId('syllabus_id')->constrained()->onDelete('cascade');
            $table->decimal('audit_score', 5, 2)->nullable();
            $table->enum('compliance_status', ['compliant', 'partially_compliant', 'non_compliant'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['quality_audit_id', 'syllabus_id']);
            $table->index(['compliance_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_audit_syllabi');
    }
};