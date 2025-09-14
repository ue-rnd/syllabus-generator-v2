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
        Schema::table('courses', function (Blueprint $table) {
            // Credit units
            $table->decimal('credit_units_lecture', 3, 1)->default(0)->after('description');
            $table->decimal('credit_units_laboratory', 3, 1)->default(0)->after('credit_units_lecture');
            
            // Course type
            $table->string('course_type')->default('pure_onsite')->after('credit_units_laboratory');
            
            // Prerequisite courses (JSON array of course IDs)
            $table->json('prerequisite_courses')->nullable()->after('course_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'credit_units_lecture',
                'credit_units_laboratory',
                'course_type',
                'prerequisite_courses'
            ]);
        });
    }
};
