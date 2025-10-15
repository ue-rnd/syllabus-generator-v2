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
        Schema::create('custom_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('report_template_id')->nullable()->constrained()->onDelete('set null');
            $table->json('report_config')->nullable();
            $table->json('filters')->nullable();
            $table->enum('scope', ['institution', 'college', 'department', 'program', 'course']);
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->enum('output_format', ['json', 'csv', 'excel', 'pdf', 'html'])->default('json');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('generated_at')->nullable();
            $table->enum('status', ['pending', 'generating', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('execution_time')->nullable();
            $table->integer('record_count')->nullable();
            $table->text('error_message')->nullable();
            $table->boolean('is_scheduled')->default(false);
            $table->foreignId('schedule_id')->nullable()->constrained('report_schedules')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'generated_at']);
            $table->index(['scope', 'scope_id']);
            $table->index(['report_template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_reports');
    }
};
