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
        Schema::create('course_program', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('course_id')->constrained('courses', 'id')->onDelete('cascade');
            $table->foreignId('program_id')->constrained('programs', 'id')->onDelete('cascade');

            // Ensure unique combination
            $table->unique(['course_id', 'program_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_program');
    }
};
