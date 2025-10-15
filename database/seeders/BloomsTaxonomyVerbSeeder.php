<?php

namespace Database\Seeders;

use App\Constants\SyllabusConstants;
use App\Models\BloomsTaxonomyVerb;
use Illuminate\Database\Seeder;

class BloomsTaxonomyVerbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $verbs = SyllabusConstants::ACTION_VERBS;
        $sortOrder = 0;

        foreach ($verbs as $category => $categoryVerbs) {
            foreach ($categoryVerbs as $key => $label) {
                BloomsTaxonomyVerb::updateOrCreate(
                    ['key' => $key], // Find by key
                    [
                        'label' => $label,
                        'category' => $category,
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
