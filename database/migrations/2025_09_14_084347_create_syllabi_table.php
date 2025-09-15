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
        Schema::create('syllabi', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('title');
            $table->string('course_code', 20)->unique();
            $table->text('description')->nullable();

            // Relationships
            $table->foreignId('course_id')->constrained('courses', 'id')->onDelete('cascade');
            $table->foreignId('college_id')->constrained('colleges', 'id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');

            // Status and Versioning
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->integer('version')->default(1);
            $table->string('academic_year', 9)->nullable(); // e.g., "2024-2025"
            $table->enum('semester', ['1st', '2nd', 'summer'])->nullable();

            // Publication
            $table->timestamp('published_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['college_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syllabi');
    }
};
