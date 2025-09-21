<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the College of Computer Studies and Systems
        $ccss = College::where('code', 'CCSS')->first();

        if (! $ccss) {
            throw new \Exception('College of Computer Studies and Systems not found. Make sure to run CollegeSeeder first.');
        }

        $departments = [
            [
                'name' => 'Department of Computer Science',
                'description' => 'The Department of Computer Science focuses on theoretical foundations of computing, algorithms, data structures, and software engineering principles.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Department of Information Technology',
                'description' => 'The Department of Information Technology emphasizes practical applications of computing technology, systems administration, and network management.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Department of Entertainment and Multimedia Computing',
                'description' => 'The Department of Entertainment and Multimedia Computing specializes in digital media, game development, and multimedia applications.',
                'sort_order' => 3,
            ],
        ];

        foreach ($departments as $department) {
            Department::create([
                'name' => $department['name'],
                'description' => $department['description'],
                'is_active' => true,
                'sort_order' => $department['sort_order'],
                'college_id' => $ccss->id,
            ]);
        }
    }
}
