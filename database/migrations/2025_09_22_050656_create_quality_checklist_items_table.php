<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_checklist_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('field_to_check');
            $table->enum('validation_rule', ['required', 'min_length', 'max_length', 'contains_keywords', 'array_min_items', 'array_max_items', 'numeric_range', 'date_range', 'format_check', 'completeness']);
            $table->json('validation_parameters')->nullable();
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['quality_checklist_id', 'is_active']);
            $table->index(['validation_rule']);
            $table->index(['is_mandatory']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_checklist_items');
    }
};
