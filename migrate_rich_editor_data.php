<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Starting data migration for RichEditor compatibility...\n";

// Convert Program outcomes and objectives from array to HTML format
$programs = DB::table('programs')->get();
foreach ($programs as $program) {
    $updates = [];
    
    // Handle outcomes
    if ($program->outcomes) {
        $outcomes = json_decode($program->outcomes, true);
        if (is_array($outcomes)) {
            $html = '<ul>';
            foreach ($outcomes as $outcome) {
                $html .= '<li>' . htmlspecialchars($outcome) . '</li>';
            }
            $html .= '</ul>';
            $updates['outcomes'] = $html;
        }
    }
    
    // Handle objectives
    if ($program->objectives) {
        $objectives = json_decode($program->objectives, true);
        if (is_array($objectives)) {
            $html = '<ul>';
            foreach ($objectives as $objective) {
                $html .= '<li>' . htmlspecialchars($objective) . '</li>';
            }
            $html .= '</ul>';
            $updates['objectives'] = $html;
        }
    }
    
    if (!empty($updates)) {
        DB::table('programs')->where('id', $program->id)->update($updates);
        echo "Updated program ID {$program->id} - " . implode(', ', array_keys($updates)) . "\n";
    }
}

echo "Data migration completed successfully!\n";
