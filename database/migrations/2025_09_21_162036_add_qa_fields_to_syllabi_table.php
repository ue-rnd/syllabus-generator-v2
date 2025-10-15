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
            $table->timestamp('qa_reviewed_at')->nullable()->after('dean_approved_at');
            $table->foreignId('qa_reviewed_by')->nullable()->after('qa_reviewed_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->dropForeign(['qa_reviewed_by']);
            $table->dropColumn(['qa_reviewed_at', 'qa_reviewed_by']);
        });
    }
};
