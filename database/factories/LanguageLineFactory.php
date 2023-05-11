<?php

namespace Database\Factories;

use App\Enums\Language;
use App\Enums\LanguageLineGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LanguageLine>
 */
class LanguageLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group' => LanguageLineGroup::randomCase(),
            'key'   => fake()->word(),
            'text'  => [
                Language::randomCase()->value => fake()->sentence(),
            ],
        ];
    }

    /**
     * Indicate that the language line's text will translate to the given locale.
     *
     * @param string $locale
     *
     * @return static
     */
    public function withLocale(string $locale): static
    {
        return $this->state(fn (array $attributes) => [
            'text' => [
                $locale => fake()->sentence(),
            ],
        ]);
    }
}
