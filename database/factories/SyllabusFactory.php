<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Syllabus>
 */
class SyllabusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Syllabus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3).' Syllabus',
            'description' => $this->faker->paragraph(2),
            'course_id' => Course::factory(),
            'default_lecture_hours' => $this->faker->randomFloat(1, 1, 4),
            'default_laboratory_hours' => $this->faker->randomFloat(1, 0, 3),
            'course_outcomes' => [
                [
                    'verb' => $this->faker->randomElement(['analyze', 'evaluate', 'create', 'understand', 'apply']),
                    'content' => $this->faker->sentence(),
                ],
                [
                    'verb' => $this->faker->randomElement(['design', 'implement', 'demonstrate', 'explain', 'solve']),
                    'content' => $this->faker->sentence(),
                ],
            ],
            'learning_matrix' => [
                [
                    'week_range' => [
                        'start' => 1,
                        'end' => 1,
                        'is_range' => false,
                    ],
                    'learning_outcomes' => [
                        [
                            'verb' => $this->faker->randomElement(['understand', 'identify', 'describe']),
                            'content' => $this->faker->sentence(),
                        ],
                    ],
                    'learning_activities' => [
                        [
                            'modality' => [$this->faker->randomElement(['onsite', 'offsite_asynchronous', 'offsite_synchronous'])],
                            'reference' => $this->faker->sentence(4),
                            'description' => $this->faker->sentence(6),
                        ],
                    ],
                    'assessments' => [$this->faker->randomElement(['quiz', 'assignment', 'project'])],
                ],
            ],
            'textbook_references' => $this->faker->paragraph(),
            'adaptive_digital_solutions' => $this->faker->paragraph(),
            'online_references' => $this->faker->paragraph(),
            'other_references' => $this->faker->paragraph(),
            'grading_system' => $this->faker->paragraph(),
            'classroom_policies' => $this->faker->paragraph(),
            'consultation_hours' => $this->faker->sentence(),
            'principal_prepared_by' => User::factory(),
            'prepared_by' => [],
            'sort_order' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->randomElement(['draft', 'pending_approval', 'approved']),
        ];
    }

    /**
     * Configure the factory for a specific course.
     */
    public function forCourse(Course $course): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => $course->id,
            'name' => $course->name.' Syllabus',
        ]);
    }

    /**
     * Configure the factory for a specific user as principal preparer.
     */
    public function preparedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'principal_prepared_by' => $user->id,
        ]);
    }

    /**
     * Set the syllabus as approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'submitted_at' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'dept_chair_reviewed_at' => $this->faker->dateTimeBetween('-1 month', '-3 weeks'),
            'assoc_dean_reviewed_at' => $this->faker->dateTimeBetween('-3 weeks', '-2 weeks'),
            'dean_approved_at' => $this->faker->dateTimeBetween('-2 weeks', '-1 week'),
            'reviewed_by' => User::factory(),
            'recommending_approval' => User::factory(),
            'approved_by' => User::factory(),
        ]);
    }

    /**
     * Set the syllabus as draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
