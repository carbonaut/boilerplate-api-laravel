<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhraseKeyRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $matches = [];
        preg_match('/^[A-Z.\-_0-9]+$/', $value, $matches);

        return count($matches) !== 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.phrase_key');
    }
}
