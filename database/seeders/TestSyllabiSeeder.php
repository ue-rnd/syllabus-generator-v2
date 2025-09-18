<?php

namespace Database\Seeders;

use App\Models\Syllabus;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSyllabiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $courses = Course::take(3)->get();

        if (!$user || $courses->isEmpty()) {
            $this->command->error('No user or courses found. Please run other seeders first.');
            return;
        }

        $syllabi = [
            [
                'name' => 'Introduction to Computer Science',
                'description' => 'A comprehensive introduction to computer science concepts and programming fundamentals.',
                'course_id' => $courses[0]->id,
                'default_lecture_hours' => 3.0,
                'default_laboratory_hours' => 0.0,
                'principal_prepared_by' => $user->id,
                'status' => 'approved',
                'submitted_at' => now()->subDays(5),
                'dean_approved_at' => now()->subDays(2),
            ],
            [
                'name' => 'Data Structures and Algorithms',
                'description' => 'Advanced course covering fundamental data structures and algorithmic problem-solving techniques.',
                'course_id' => $courses[1]->id ?? $courses[0]->id,
                'default_lecture_hours' => 3.0,
                'default_laboratory_hours' => 1.0,
                'principal_prepared_by' => $user->id,
                'status' => 'under_review',
                'submitted_at' => now()->subDays(3),
            ],
            [
                'name' => 'Database Management Systems',
                'description' => 'Introduction to database design, implementation, and management using modern database systems.',
                'course_id' => $courses[2]->id ?? $courses[0]->id,
                'default_lecture_hours' => 2.0,
                'default_laboratory_hours' => 2.0,
                'principal_prepared_by' => $user->id,
                'status' => 'draft',
            ],
        ];

        foreach ($syllabi as $syllabusData) {
            Syllabus::create($syllabusData);
        }

        $this->command->info('Created ' . count($syllabi) . ' test syllabi.');
    }
}
