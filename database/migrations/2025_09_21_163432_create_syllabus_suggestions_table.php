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
        Schema::create('syllabus_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syllabus_id')->constrained()->onDelete('cascade');
            $table->foreignId('suggested_by')->constrained('users')->onDelete('cascade');
            $table->string('field_name'); // Which field is being suggested to change
            $table->longText('current_value')->nullable(); // Current value in the field
            $table->longText('suggested_value'); // Proposed new value
            $table->text('reason')->nullable(); // Why this change is suggested
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('review_comments')->nullable();
            $table->json('metadata')->nullable(); // Store additional context like array indices for complex fields
            $table->timestamps();

            // Indexes for performance
            $table->index(['syllabus_id', 'status']);
            $table->index(['suggested_by', 'status']);
            $table->index('field_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syllabus_suggestions');
    }
};
