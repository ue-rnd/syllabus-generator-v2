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
        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content');
            $table->string('category'); // getting_started, syllabus_creation, etc.
            $table->string('difficulty_level'); // beginner, intermediate, advanced
            $table->integer('duration_minutes')->nullable();
            $table->string('video_url')->nullable();
            $table->json('attachments')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('featured')->default(false);
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('views_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['category']);
            $table->index(['difficulty_level']);
            $table->index(['is_published']);
            $table->index(['featured']);
            $table->index(['author_id']);
            $table->index(['views_count']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorials');
    }
};
