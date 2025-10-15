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
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('name')->unique();
            $table->string('code', 10)->unique();
            $table->text('description')->nullable();

            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->text('core_values')->nullable();

            $table->json('objectives')->nullable();

            // Status and Configuration
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('logo_path')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
