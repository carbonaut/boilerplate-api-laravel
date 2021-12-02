<?php

namespace App\Http\Traits;

trait TranslationAccessors
{
    /**
     * Return all translations for a given attribute.
     *
     * @param string $attribute
     *
     * @return array
     */
    public function getTranslations(string $attribute)
    {
        return $this->translations->pluck('locale')->mapWithKeys(function ($locale, $key) use ($attribute) {
            return [$locale => $this->translateOrDefault($locale)->{$attribute}];
        })->toArray();
    }

    /**
     * Set all translations for a given attribute.
     *
     * @param string $realAttribute
     * @param array  $translations
     */
    public function setTranslations(string $realAttribute, array $translations)
    {
        foreach ($translations as $locale => $value) {
            $this->{"{$realAttribute}:{$locale}"} = $value;
        }
    }
}
