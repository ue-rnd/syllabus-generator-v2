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
        Schema::table('colleges', function (Blueprint $table) {
            // Add dean and associate dean fields
            $table->foreignId('dean_id')->nullable()->after('logo_path')->constrained('users')->onDelete('set null');
            $table->foreignId('associate_dean_id')->nullable()->after('dean_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropForeign(['dean_id']);
            $table->dropForeign(['associate_dean_id']);
            $table->dropColumn(['dean_id', 'associate_dean_id']);
        });
    }
};
