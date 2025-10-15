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
        Schema::create('tutorial_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutorial_id')->constrained('tutorials')->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->integer('step_order')->default(0);
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->text('code_snippet')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tutorial_id', 'step_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorial_steps');
    }
};
