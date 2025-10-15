<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('report_type', ['compliance_summary', 'quality_audit', 'standards_assessment', 'progress_tracking', 'custom']);
            $table->enum('scope', ['institution', 'college', 'department', 'program', 'course']);
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->json('filters')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('generated_at')->nullable();
            $table->enum('status', ['pending', 'generating', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('file_path')->nullable();
            $table->enum('file_format', ['pdf', 'excel', 'csv'])->nullable();
            $table->json('summary')->nullable();
            $table->integer('total_syllabi')->default(0);
            $table->integer('compliant_syllabi')->default(0);
            $table->integer('non_compliant_syllabi')->default(0);
            $table->decimal('compliance_rate', 5, 2)->default(0);
            $table->boolean('auto_generated')->default(false);
            $table->foreignId('schedule_id')->nullable()->constrained('report_schedules')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['report_type', 'status']);
            $table->index(['scope', 'scope_id']);
            $table->index(['generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_reports');
    }
};
