<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('syllabi')
            ->select(['id', 'program_outcomes'])
            ->orderBy('id')
            ->whereNotNull('program_outcomes')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $data = json_decode($row->program_outcomes, true);
                    if (!is_array($data)) {
                        continue;
                    }

                    $changed = false;

                    foreach ($data as $index => $outcome) {
                        if (!is_array($outcome)) {
                            continue;
                        }

                        $addressed = $outcome['addressed'] ?? [];
                        if (!is_array($addressed)) {
                            continue;
                        }

                        $normalized = array_map(function ($value) {
                            // Normalize to canonical lowercase keys used across the app
                            $mapExact = [
                                'Introduce' => 'introduced',
                                'Introduced' => 'introduced',
                                'I' => 'introduced',
                                'Enhance' => 'enhanced',
                                'Enhanced' => 'enhanced',
                                'E' => 'enhanced',
                                'Demonstrate' => 'demonstrated',
                                'Demonstrated' => 'demonstrated',
                                'D' => 'demonstrated',
                            ];

                            if (is_string($value) && isset($mapExact[$value])) {
                                return $mapExact[$value];
                            }

                            $lv = is_string($value) ? strtolower($value) : $value;

                            if ($lv === 'introduce' || $lv === 'introduced') {
                                return 'introduced';
                            }
                            if ($lv === 'enhance' || $lv === 'enhanced') {
                                return 'enhanced';
                            }
                            if ($lv === 'demonstrate' || $lv === 'demonstrated') {
                                return 'demonstrated';
                            }

                            return $lv; // fall back to lowercase string or original value
                        }, $addressed);

                        // Ensure uniqueness and stable order
                        $normalized = array_values(array_unique($normalized));

                        if ($normalized !== $addressed) {
                            $data[$index]['addressed'] = $normalized;
                            $changed = true;
                        }
                    }

                    if ($changed) {
                        DB::table('syllabi')->where('id', $row->id)->update([
                            'program_outcomes' => json_encode($data),
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Optionally revert to capitalized display labels (not required), implement conservative reverse
        DB::table('syllabi')
            ->select(['id', 'program_outcomes'])
            ->orderBy('id')
            ->whereNotNull('program_outcomes')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $data = json_decode($row->program_outcomes, true);
                    if (!is_array($data)) {
                        continue;
                    }

                    $changed = false;

                    foreach ($data as $index => $outcome) {
                        if (!is_array($outcome)) {
                            continue;
                        }

                        $addressed = $outcome['addressed'] ?? [];
                        if (!is_array($addressed)) {
                            continue;
                        }

                        $reverted = array_map(function ($value) {
                            $lv = is_string($value) ? strtolower($value) : $value;
                            return match ($lv) {
                                'introduced' => 'Introduced',
                                'enhanced' => 'Enhanced',
                                'demonstrated' => 'Demonstrated',
                                default => $value,
                            };
                        }, $addressed);

                        if ($reverted !== $addressed) {
                            $data[$index]['addressed'] = $reverted;
                            $changed = true;
                        }
                    }

                    if ($changed) {
                        DB::table('syllabi')->where('id', $row->id)->update([
                            'program_outcomes' => json_encode($data),
                        ]);
                    }
                }
            });
    }
};



