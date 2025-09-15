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
            $table->tinyInteger('week_prelim')->default(0);
            $table->tinyInteger('week_midterm')->default(0);
            $table->tinyInteger('week_final')->default(0);

            $table->tinyInteger('ay_start')->default(0);
            $table->tinyInteger('ay_end')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->dropColumn('week_prelim', 'week_midterm', 'week_final', 'ay_start', 'ay_end');
        });
    }
};
