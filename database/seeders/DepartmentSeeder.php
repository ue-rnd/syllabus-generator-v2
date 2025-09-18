<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Department;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Find the College of Computer Studies and Systems
        $ccss = College::where('code', 'CCSS')->first();

        if (! $ccss) {
            throw new \Exception('College of Computer Studies and Systems not found. Make sure to run CollegeSeeder first.');
        }

        $departments = [
            [
                'name' => 'Department of Computer Science',
                'sort_order' => 1,
            ],
            [
                'name' => 'Department of Information Technology',
                'sort_order' => 2,
            ],
            [
                'name' => 'Department of Entertainment and Multimedia Computing',
                'sort_order' => 3,
            ],
        ];

        foreach ($departments as $department) {
            Department::create([
                'name' => $department['name'],
                'description' => $faker->paragraph(3),
                'is_active' => true,
                'sort_order' => $department['sort_order'],
                'college_id' => $ccss->id,
            ]);
        }
    }
}
