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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('name')->unique();
            $table->enum('level', ['ASSOCIATE','BACHELOR', 'MASTERAL', 'DOCTORAL'])->default('ASSOCIATE');
            $table->string('code', 10)->unique(); 
            $table->text('description')->nullable();

            $table->json('outcomes')->nullable();
            $table->json('objectives')->nullable();

            // Status and Configuration
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            // Department
            $table->foreignId('department_id')->constrained('departments', 'id')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
