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
            $table->string('name');
            $table->string('version', 50)->default('1.0');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);

            // Course relationship
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            // Course Outcomes (list of text, each starting with action verb)
            $table->json('course_outcomes')->nullable();

            // Learning Matrix (array of weeks/items)
            $table->json('learning_matrix')->nullable();

            // References
            $table->json('textbook_references')->nullable();
            $table->json('adaptive_digital_solutions')->nullable();
            $table->json('online_references')->nullable();
            $table->json('other_references')->nullable();

            // Grading System (rich text with formulas)
            $table->longText('grading_system')->nullable();

            // Policies and Consultation
            $table->text('classroom_policies')->nullable();
            $table->text('consultation_hours')->nullable();

            // Creators/Signers - flexible prepared by structure
            $table->foreignId('principal_prepared_by')->constrained('users')->onDelete('cascade'); // Current user (main preparer)
            $table->json('prepared_by')->nullable(); // Array of {user_id, description/role}
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // Department chair
            $table->foreignId('recommending_approval')->nullable()->constrained('users')->onDelete('set null'); // Associate dean
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Dean

            // Metadata
            $table->integer('sort_order')->default(0);
            
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['course_id', 'is_active']);
            $table->index(['course_id', 'version']);
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
