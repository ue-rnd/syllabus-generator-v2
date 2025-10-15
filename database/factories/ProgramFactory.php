<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $levels = ['associate', 'bachelor', 'masteral', 'doctoral'];

        return [
            'name' => $this->faker->unique()->words(3, true).' Program',
            'level' => $this->faker->randomElement($levels),
            'code' => $this->faker->unique()->lexify('????'),
            'description' => $this->faker->paragraphs(2, true),
            'outcomes' => [
                $this->faker->sentence(),
                $this->faker->sentence(),
                $this->faker->sentence(),
                $this->faker->sentence(),
            ],
            'objectives' => [
                $this->faker->sentence(),
                $this->faker->sentence(),
                $this->faker->sentence(),
            ],
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'sort_order' => $this->faker->numberBetween(1, 30),
            'department_id' => \App\Models\Department::factory(),
        ];
    }

    /**
     * Indicate that the program is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the program is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set a specific level.
     */
    public function level(string $level): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => $level,
        ]);
    }

    /**
     * Create an associate level program.
     */
    public function associate(): static
    {
        return $this->level('associate');
    }

    /**
     * Create a bachelor level program.
     */
    public function bachelor(): static
    {
        return $this->level('bachelor');
    }

    /**
     * Create a masteral level program.
     */
    public function masteral(): static
    {
        return $this->level('masteral');
    }

    /**
     * Create a doctoral level program.
     */
    public function doctoral(): static
    {
        return $this->level('doctoral');
    }

    /**
     * Set a specific department.
     */
    public function forDepartment(\App\Models\Department $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => $department->id,
        ]);
    }

    /**
     * Set a specific sort order.
     */
    public function sortOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }
}
