<?php

namespace Database\Seeders;

use App\Models\College;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CollegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $colleges = [
            [
                'name' => 'Graduate School',
                'code' => 'GS',
                'logo_path' => 'images/logos/logo_gs.png',
                'sort_order' => 1,
            ],
            [
                'name' => 'College of Law',
                'code' => 'CLaw',
                'logo_path' => 'images/logos/logo_claw.png',
                'sort_order' => 2,
            ],
            [
                'name' => 'College of Dentistry',
                'code' => 'CDent',
                'logo_path' => 'images/logos/logo_cdent.png',
                'sort_order' => 3,
            ],
            [
                'name' => 'College of Arts and Sciences',
                'code' => 'CAS',
                'logo_path' => 'images/logos/logo_cas.png',
                'sort_order' => 4,
            ],
            [
                'name' => 'College of Business Administration',
                'code' => 'CBA',
                'logo_path' => 'images/logos/logo_cba.png',
                'sort_order' => 5,
            ],
            [
                'name' => 'College of Computer Studies and Systems',
                'code' => 'CCSS',
                'logo_path' => 'images/logos/logo_ccss.png',
                'sort_order' => 6,
            ],
            [
                'name' => 'College of Education',
                'code' => 'CEduc',
                'logo_path' => 'images/logos/logo_ceduc.png',
                'sort_order' => 7,
            ],
            [
                'name' => 'College of Engineering',
                'code' => 'CEng\'g',
                'logo_path' => 'images/logos/logo_cengg.png',
                'sort_order' => 8,
            ],
            [
                'name' => 'Basic Education Department',
                'code' => 'BasicEd.',
                'logo_path' => 'images/logos/logo_be.png',
                'sort_order' => 9,
            ],
        ];

        foreach ($colleges as $college) {
            College::create([
                'name' => $college['name'],
                'code' => $college['code'],
                'description' => $faker->paragraph(3),
                'mission' => $faker->paragraph(4),
                'vision' => $faker->paragraph(2),
                'core_values' => $faker->paragraph(3),
                'objectives' => [
                    $faker->sentence(),
                    $faker->sentence(),
                    $faker->sentence(),
                    $faker->sentence(),
                ],
                'is_active' => true,
                'sort_order' => $college['sort_order'],
                'logo_path' => $college['logo_path'],
            ]);
        }
    }
}
