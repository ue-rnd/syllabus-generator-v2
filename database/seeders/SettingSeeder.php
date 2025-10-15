<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'key' => 'default_ay_start',
            'label' => 'Default AY Start',
            'value' => 2025,
            'type' => 'number',
            'sort_order' => 1,
            'category' => 'academic',
        ]);

        Setting::create([
            'key' => 'default_ay_end',
            'label' => 'Default AY End',
            'value' => 2026,
            'type' => 'number',
            'sort_order' => 2,
            'category' => 'academic',
        ]);

        Setting::create([
            'key' => 'default_week_prelim',
            'label' => 'Default Prelims Exam Week',
            'value' => 6,
            'type' => 'number',
            'sort_order' => 3,
            'category' => 'academic',
        ]);

        Setting::create([
            'key' => 'default_week_midterm',
            'label' => 'Default Midterms Exam Week',
            'value' => 12,
            'type' => 'number',
            'sort_order' => 4,
            'category' => 'academic',
        ]);

        Setting::create([
            'key' => 'default_week_final',
            'label' => 'Default Finals Exam Week',
            'value' => 18,
            'type' => 'number',
            'sort_order' => 5,
            'category' => 'academic',
        ]);

        Setting::create([
            'key' => 'university_mission',
            'label' => 'University Mission',
            'value' => '<p> Imploring the aid of Divine Providence, the University of the East dedicates itself to the service of youth, country, and God, and declares adherence to academic freedom, progressive instruction, creative scholarship, goodwill among nations, and constructive educational leadership.</p><p> Inspired and sustained by a deep sense of dedication and a compelling yearning for relevance, the University of the East hereby declares as its goal and addresses itself to the development of a just, progressive, and humane society.</p>',
            'type' => 'richtext',
            'sort_order' => 6,
            'category' => 'metadata',
        ]);

        Setting::create([
            'key' => 'university_vision',
            'label' => 'University Vision',
            'value' => '<p>As a private non-sectarian institution of higher learning, the University of the East commits itself to producing, through relevant and affordable quality education, morally upright and competent leaders in various professions, imbued with a strong sense of service to their fellowmen and their country.</p>',
            'type' => 'richtext',
            'sort_order' => 6,
            'category' => 'metadata',
        ]);

        Setting::create([
            'key' => 'university_core_values',
            'label' => 'University Core Values',
            'value' => '<p>The University adheres to the core values of Excellence, Integrity, Professionalism, Teamwork, Commitment, Transparency, Accountability, and Social Responsibility.</p>',
            'type' => 'richtext',
            'sort_order' => 7,
            'category' => 'metadata',
        ]);

        Setting::create([
            'key' => 'university_guiding_principles',
            'label' => 'University Guiding Principles',
            'value' => "<p>The Institution declares the following to be its guiding principles:</p><ol><li><p>Dedication forever to the service of youth, country, and God; training the youth to become good and competent citizens; promoting a deep and abiding loyalty to the Motherland and her own way of life; and serving the will of the Creator;</p></li><li><p>Active encouragement of academic freedom, the only road to the realm of wisdom and truth;</p></li><li><p>Constant attunement of curricula to the changing needs of individuals and nations in civilizations and cultures ceaselessly being enriched by technology, science, and scholarship;</p></li><li><p>Encouragement to the utmost of scholarship and research toward the broadening of knowledge to new horizons and the augmenting of mankind's harvest of freedom, contentment, and abundance;</p></li><li><p>Promotion, through the bonds of culture, of international amity and goodwill as basis for the enduring world peace long dreamed of by men; and</p></li><li><p>Uttermost endeavor to attain and keep a position at the vanguard of higher education so that, as a beacon light to all the Orient, it may attract to its campuses promising youth from many lands in search of wisdom and truth.</p></li></ol>",
            'type' => 'richtext',
            'sort_order' => 8,
            'category' => 'metadata',
        ]);

        Setting::create([
            'key' => 'university_institutional_outcomes',
            'label' => 'University Institutional Outcomes',
            'value' => "<p>In pursuit of its vision and mission, the University will produce GRADUATES</p><ul><li><p>attuned to the constantly changing needs and challenges of the youth within the context of a proud nation, its enriched culture in the global community;</p></li><li><p>able to produce new knowledge gleaned from innovative research – the hallmark of an institution's integrity and dynamism; and</p></li><li><p>capable of rendering relevant and committed service to the community, the nation, and the world.</p></li></ul>",
            'type' => 'richtext',
            'sort_order' => 9,
            'category' => 'metadata',
        ]);
    }
}
