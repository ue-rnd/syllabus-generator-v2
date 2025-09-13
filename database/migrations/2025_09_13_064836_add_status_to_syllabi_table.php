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
            // Add status enum field - Draft, Pending Approval, Rejected, For Revisions, Approved
            $table->enum('status', ['draft', 'pending_approval', 'rejected', 'for_revisions', 'approved'])
                  ->default('draft')
                  ->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
