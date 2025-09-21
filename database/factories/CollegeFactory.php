<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\College>
 */
class CollegeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true).' College',
            'code' => $this->faker->unique()->lexify('???'),
            'description' => $this->faker->paragraphs(2, true),
            'mission' => $this->faker->paragraphs(3, true),
            'vision' => $this->faker->paragraphs(2, true),
            'core_values' => $this->faker->paragraphs(2, true),
            'objectives' => [
                $this->faker->sentence(),
                $this->faker->sentence(),
                $this->faker->sentence(),
            ],
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'sort_order' => $this->faker->numberBetween(1, 100),
            'logo_path' => null, // Can be set manually when needed
        ];
    }

    /**
     * Indicate that the college is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the college is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
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
