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
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('report_type', ['compliance', 'quality', 'analytics', 'audit', 'custom']);
            $table->json('template_config')->nullable();
            $table->json('default_filters')->nullable();
            $table->enum('default_scope', ['institution', 'college', 'department', 'program', 'course'])->nullable();
            $table->enum('output_format', ['pdf', 'excel', 'csv', 'json', 'html'])->default('pdf');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_system')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('college_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['report_type', 'is_public']);
            $table->index(['college_id', 'is_public']);
            $table->index(['department_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
