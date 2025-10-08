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
        Schema::create('form_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('form_key');
            $table->integer('current_step')->default(1);
            $table->json('data');
            $table->integer('version')->default(1);
            $table->string('lock_token')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            // Unique index on (user_id, form_key) to prevent duplicate drafts
            $table->unique(['user_id', 'form_key']);
            
            // Index for cleanup jobs
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_drafts');
    }
};