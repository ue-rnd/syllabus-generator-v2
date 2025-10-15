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
        Schema::create('syllabus_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_name');
            $table->decimal('metric_value', 10, 2);
            $table->enum('metric_type', ['count', 'percentage', 'score', 'duration', 'rate']);
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('scope', ['institution', 'college', 'department', 'program', 'course', 'user']);
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->index(['metric_name', 'scope']);
            $table->index(['period_start', 'period_end']);
            $table->index(['calculated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syllabus_metrics');
    }
};
