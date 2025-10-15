<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_standards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->enum('type', ['institutional', 'accreditation', 'departmental', 'program', 'course']);
            $table->enum('category', ['content', 'structure', 'assessment', 'learning_outcomes', 'resources', 'policies']);
            $table->json('criteria')->nullable();
            $table->decimal('minimum_score', 5, 2)->default(0);
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->foreignId('college_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['college_id', 'is_active']);
            $table->index(['department_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_standards');
    }
};