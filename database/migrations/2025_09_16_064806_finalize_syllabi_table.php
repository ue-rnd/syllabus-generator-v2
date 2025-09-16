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
        Schema::table('syllabi', function (Blueprint $table) {
            $table->dropColumn('default_classroom_policies', 'default_consultation_hours', 'default_grading_system');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->string('default_classroom_policies')->nullable();
            $table->string('default_consultation_hours')->nullable();
            $table->string('default_grading_system')->nullable();
        });
    }
};
