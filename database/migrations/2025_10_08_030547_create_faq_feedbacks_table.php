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
        Schema::create('faq_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_helpful')->nullable();
            $table->text('comment')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['faq_id']);
            $table->index(['user_id']);
            $table->index(['is_helpful']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_feedbacks');
    }
};
