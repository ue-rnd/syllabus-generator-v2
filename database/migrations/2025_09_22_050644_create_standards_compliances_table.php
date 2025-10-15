<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standards_compliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syllabus_id')->constrained()->onDelete('cascade');
            $table->foreignId('quality_standard_id')->constrained()->onDelete('cascade');
            $table->enum('compliance_status', ['not_assessed', 'compliant', 'partially_compliant', 'non_compliant'])->default('not_assessed');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('checked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('checked_at')->nullable();
            $table->json('evidence')->nullable();
            $table->boolean('remediation_required')->default(false);
            $table->text('remediation_notes')->nullable();
            $table->timestamps();

            $table->unique(['syllabus_id', 'quality_standard_id']);
            $table->index(['compliance_status']);
            $table->index(['checked_at']);
            $table->index(['remediation_required']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standards_compliances');
    }
};
