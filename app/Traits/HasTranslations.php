<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait HasTranslations
{
    use BaseHasTranslations;

    /**
     * Convert the model instance to an array.
     * https://spatie.be/docs/laravel-translatable/v6/advanced-usage/customize-the-toarray-method.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        // Fetch the parent's attributes;
        $attributes = parent::toArray();

        // For each translatable attribute, translate it to the current locale;
        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->getTranslation($field, App::getLocale());
        }

        // Return the translated attributes;
        return $attributes;
    }
}
