<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstname = fake()->firstName();
        $lastname = fake()->lastName();
        $middlename = fake()->optional(0.3)->firstName(); // 30% chance of having a middle name
        
        // Build full name
        $name = $firstname;
        if ($middlename) {
            $name .= ' ' . $middlename;
        }
        $name .= ' ' . $lastname;
        
        return [
            'name' => $name,
            'lastname' => $lastname,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'position' => fake()->jobTitle(),
            'is_active' => fake()->boolean(),
            'last_login_at' => fake()->dateTime(),
            'last_login_ip' => fake()->ipv4(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
