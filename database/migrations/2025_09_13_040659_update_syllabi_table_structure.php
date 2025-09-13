<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            // Drop indexes that reference the columns we're dropping
            $table->dropIndex(['course_id', 'version']);
            $table->dropIndex(['course_id', 'is_active']);
            
            // Remove version and is_active fields
            $table->dropColumn(['version', 'is_active']);
            
            // Add global lecture and laboratory hours (single input, duplicated across weeks)
            $table->decimal('default_lecture_hours', 3, 1)->default(0)->after('description');
            $table->decimal('default_laboratory_hours', 3, 1)->default(0)->after('default_lecture_hours');
            
            // Add prefilled policy fields with default content
            $table->longText('default_classroom_policies')->nullable()->after('consultation_hours');
            $table->longText('default_consultation_hours')->nullable()->after('default_classroom_policies');
            $table->longText('default_grading_system')->nullable()->after('default_consultation_hours');
            
            // Add new index for course_id only
            $table->index(['course_id']);
        });

        // Set default values for the new policy fields
        DB::table('syllabi')->update([
            'default_classroom_policies' => "1. Attendance is mandatory for all class sessions.\n2. Late submissions will be penalized according to the course policy.\n3. Academic integrity must be maintained at all times.\n4. Respectful behavior is expected from all students.\n5. Electronic devices should be used for academic purposes only during class.",
            'default_consultation_hours' => "Monday to Friday: 2:00 PM - 4:00 PM\nBy appointment: Contact through official email\nResponse time: Within 24-48 hours for email inquiries",
            'default_grading_system' => "<table><tr><th>Component</th><th>Percentage</th></tr><tr><td>Class Participation</td><td>10%</td></tr><tr><td>Quizzes & Assignments</td><td>30%</td></tr><tr><td>Midterm Examination</td><td>25%</td></tr><tr><td>Final Examination</td><td>35%</td></tr></table>\n\n<strong>Grading Scale:</strong><br>A: 90-100<br>B: 80-89<br>C: 70-79<br>D: 60-69<br>F: Below 60"
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            // Remove the new index
            $table->dropIndex(['course_id']);
            
            // Add back the removed fields
            $table->string('version', 50)->default('1.0')->after('description');
            $table->boolean('is_active')->default(false)->after('version');
            
            // Remove the new fields
            $table->dropColumn([
                'default_lecture_hours',
                'default_laboratory_hours',
                'default_classroom_policies',
                'default_consultation_hours',
                'default_grading_system'
            ]);
            
            // Add back the original indexes
            $table->index(['course_id', 'is_active']);
            $table->index(['course_id', 'version']);
        });
    }
};
