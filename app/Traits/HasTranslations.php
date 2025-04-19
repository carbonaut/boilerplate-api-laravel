<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait HasTranslations
{
    use BaseHasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var list<string>
     */
    protected array $translatable = [];

    /**
     * Convert the model instance to an array.
     * https://spatie.be/docs/laravel-translatable/v6/advanced-usage/customize-the-toarray-method.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        // Fetch the parent's attributes;
        /** @var array<string, mixed> $attributes */
        $attributes = parent::toArray();

        // For each translatable attribute, translate it to the current locale;
        foreach ($this->translatable as $field) {
            $attributes[$field] = $this->getTranslation($field, App::getLocale());
        }

        // Return the translated attributes;
        return $attributes;
    }
}
