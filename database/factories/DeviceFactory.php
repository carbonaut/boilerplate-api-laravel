<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'uuid'             => Str::uuid()->toString(),
            'name'             => fake()->name(),
            'platform'         => fake()->randomElement(['iOS', 'Android', 'Web']),
            'operating_system' => fake()->randomElement(['iOS', 'Android', 'Windows', 'macOS', 'Linux']),
            'os_version'       => fake()->numerify('#.#.#'),
            'manufacturer'     => fake()->company(),
            'model'            => fake()->word(),
            'web_view_version' => fake()->semver(),
            'app_version'      => fake()->semver(),
            'is_virtual'       => fake()->boolean(),
            'push_token'       => fake()->unique()->sha256(),
            'is_active'        => fake()->boolean(),
        ];
    }
}
