<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get departments
        $dcs = Department::where('name', 'Department of Computer Science')->first();
        $dit = Department::where('name', 'Department of Information Technology')->first();
        $dis = Department::where('name', 'Department of Information Systems')->first();

        if (!$dcs || !$dit || !$dis) {
            throw new \Exception('Departments not found. Make sure to run DepartmentSeeder first.');
        }

        $programs = [
            // Computer Science Department Programs
            [
                'name' => 'Bachelor of Science in Computer Science',
                'code' => 'BSCS',
                'level' => 'BACHELOR',
                'department_id' => $dcs->id,
                'sort_order' => 1,
            ],
            [
                'name' => 'Master of Science in Computer Science',
                'code' => 'MSCS',
                'level' => 'MASTERAL',
                'department_id' => $dcs->id,
                'sort_order' => 2,
            ],
            
            // Information Technology Department Programs
            [
                'name' => 'Bachelor of Science in Information Technology',
                'code' => 'BSIT',
                'level' => 'BACHELOR',
                'department_id' => $dit->id,
                'sort_order' => 3,
            ],
            
            // Information Systems Department Programs
            [
                'name' => 'Bachelor of Science in Information Systems',
                'code' => 'BSIS',
                'level' => 'BACHELOR',
                'department_id' => $dis->id,
                'sort_order' => 4,
            ],
        ];

        foreach ($programs as $program) {
            Program::create([
                'name' => $program['name'],
                'code' => $program['code'],
                'level' => $program['level'],
                'description' => $faker->paragraph(3),
                'objectives' => [
                    $faker->sentence(),
                    $faker->sentence(),
                    $faker->sentence(),
                ],
                'outcomes' => [
                    $faker->sentence(),
                    $faker->sentence(),
                    $faker->sentence(),
                ],
                'is_active' => true,
                'sort_order' => $program['sort_order'],
                'department_id' => $program['department_id'],
            ]);
        }
    }
}
