<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            // Approval workflow timestamps
            $table->timestamp('submitted_at')->nullable()->after('status');
            $table->timestamp('dept_chair_reviewed_at')->nullable()->after('submitted_at');
            $table->timestamp('assoc_dean_reviewed_at')->nullable()->after('dept_chair_reviewed_at');
            $table->timestamp('dean_approved_at')->nullable()->after('assoc_dean_reviewed_at');
            
            // Rejection/revision tracking
            $table->json('approval_history')->nullable()->after('dean_approved_at');
            $table->text('rejection_comments')->nullable()->after('approval_history');
            $table->string('rejected_by_role')->nullable()->after('rejection_comments');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by_role');
            
            // Version tracking for revisions
            $table->integer('version')->default(1)->after('rejected_at');
            $table->unsignedBigInteger('parent_syllabus_id')->nullable()->after('version');
            
            // Add foreign key for parent syllabus (for revision tracking)
            $table->foreign('parent_syllabus_id')->references('id')->on('syllabi')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index(['status', 'submitted_at']);
            $table->index(['course_id', 'version']);
            $table->index('parent_syllabus_id');
        });
    }

    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->dropForeign(['parent_syllabus_id']);
            $table->dropColumn([
                'submitted_at',
                'dept_chair_reviewed_at',
                'assoc_dean_reviewed_at',
                'dean_approved_at',
                'approval_history',
                'rejection_comments',
                'rejected_by_role',
                'rejected_at',
                'version',
                'parent_syllabus_id'
            ]);
        });
    }
};
