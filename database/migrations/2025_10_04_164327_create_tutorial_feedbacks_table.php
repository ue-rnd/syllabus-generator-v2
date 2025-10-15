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
        Schema::create('tutorial_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutorial_id')->constrained('tutorials')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('is_helpful')->nullable();
            $table->integer('rating')->nullable();
            $table->text('comment')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->index(['tutorial_id']);
            $table->index(['user_id']);
            $table->index(['is_helpful']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorial_feedbacks');
    }
};
