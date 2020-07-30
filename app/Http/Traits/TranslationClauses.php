<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TranslationClauses {
    /**
     * This scope filters results by checking the translation fields (case-insensitive).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $key
     * @param string                                $value
     * @param string                                $locale
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeWhereTranslationILike(Builder $query, $key, $value, $locale = null) {
        return $query->whereHas('translations', function (Builder $query) use ($key, $value, $locale) {
            $query->where($this->getTranslationsTable() . '.' . $key, 'ILIKE', $value);

            if ($locale) {
                $query->where($this->getTranslationsTable() . '.' . $this->getLocaleKey(), 'LIKE', $locale);
            }
        });
    }

    /**
     * This scope filters results by checking the translation fields (case-insensitive).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $key
     * @param string                                $value
     * @param string                                $locale
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeOrWhereTranslationILike(Builder $query, $key, $value, $locale = null) {
        return $query->orWhereHas('translations', function (Builder $query) use ($key, $value, $locale) {
            $query->where($this->getTranslationsTable() . '.' . $key, 'ILIKE', $value);

            if ($locale) {
                $query->where($this->getTranslationsTable() . '.' . $this->getLocaleKey(), 'LIKE', $locale);
            }
        });
    }
}
