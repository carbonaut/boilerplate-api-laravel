<?php

namespace Database\Factories;

use App\Enums\Language;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'           => fake()->name(),
            'email'          => fake()->unique()->safeEmail(),
            'language'       => Language::randomCase()->value,
            'password'       => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be verified.
     *
     * @return static
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at'                  => now(),
            'email_verification_code'            => null,
            'email_verification_code_expires_at' => null,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at'                  => null,
            'email_verification_code'            => rand(100000, 999999),
            'email_verification_code_expires_at' => now()->addHour(),
        ]);
    }
}
