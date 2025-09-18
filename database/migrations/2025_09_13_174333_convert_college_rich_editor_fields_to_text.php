<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert data for each college
        $colleges = DB::table('colleges')->get();

        foreach ($colleges as $college) {
            $updates = [];

            // Handle objectives - convert from JSON array to HTML
            if ($college->objectives) {
                $objectives = json_decode($college->objectives, true);
                if (is_array($objectives)) {
                    $html = '<ul>';
                    foreach ($objectives as $objective) {
                        $html .= '<li>'.htmlspecialchars($objective).'</li>';
                    }
                    $html .= '</ul>';
                    $updates['objectives'] = $html;
                }
            }

            if (! empty($updates)) {
                DB::table('colleges')->where('id', $college->id)->update($updates);
            }
        }

        // Then modify the schema to change from JSON to TEXT
        Schema::table('colleges', function (Blueprint $table) {
            $table->text('objectives')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert schema changes
        Schema::table('colleges', function (Blueprint $table) {
            $table->json('objectives')->nullable()->change();
        });

        // Note: We're not reverting the data conversion as it would be lossy
        // Manual intervention would be required to restore original array format
    }
};
