<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\College;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Find the College of Computer Studies and Systems
        $ccss = College::where('code', 'CCSS')->first();
        
        if (!$ccss) {
            throw new \Exception('College of Computer Studies and Systems not found.');
        }

        // Get programs to attach courses to
        $bscs = Program::where('code', 'BSCS')->first();
        $bsit = Program::where('code', 'BSIT')->first();
        $bsis = Program::where('code', 'BSIS')->first();
        $mscs = Program::where('code', 'MSCS')->first();

        $courses = [
            [
                'name' => 'Introduction to Programming',
                'code' => 'CS101',
                'programs' => [$bscs, $bsit, $bsis], // Shared by multiple programs
                'sort_order' => 1,
            ],
            [
                'name' => 'Data Structures and Algorithms',
                'code' => 'CS201',
                'programs' => [$bscs, $mscs], // CS specific
                'sort_order' => 2,
            ],
            [
                'name' => 'Database Systems',
                'code' => 'IT301',
                'programs' => [$bsit, $bsis], // IT and IS specific
                'sort_order' => 3,
            ],
            [
                'name' => 'Software Engineering',
                'code' => 'CS301',
                'programs' => [$bscs, $bsit], // CS and IT
                'sort_order' => 4,
            ],
            [
                'name' => 'Systems Analysis and Design',
                'code' => 'IS401',
                'programs' => [$bsis], // IS specific
                'sort_order' => 5,
            ],
            [
                'name' => 'Machine Learning',
                'code' => 'CS501',
                'programs' => [$mscs], // Graduate level
                'sort_order' => 6,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::create([
                'name' => $courseData['name'],
                'code' => $courseData['code'],
                'description' => $faker->paragraph(3),
                'outcomes' => [
                    $faker->sentence(),
                    $faker->sentence(),
                    $faker->sentence(),
                ],
                'is_active' => true,
                'sort_order' => $courseData['sort_order'],
                'college_id' => $ccss->id,
            ]);

            // Attach the course to multiple programs (many-to-many relationship)
            foreach ($courseData['programs'] as $program) {
                if ($program) {
                    $course->programs()->attach($program->id);
                }
            }
        }
    }
}
