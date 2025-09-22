<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_audits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('audit_type', ['compliance', 'quality_improvement', 'accreditation', 'internal', 'external']);
            $table->enum('scope', ['institution', 'college', 'department', 'program', 'course']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->foreignId('auditor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('college_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('criteria')->nullable();
            $table->text('summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['audit_type', 'status']);
            $table->index(['scope', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['college_id', 'status']);
            $table->index(['department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_audits');
    }
};
