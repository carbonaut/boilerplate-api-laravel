<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class PasswordStrength implements Rule {
    /**
     * Create a new rule instance.
     */
    public function __construct() {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value) {
        if (Str::length($value) < 8 || !preg_match('/\p{Lu}/u', $value) || !preg_match('/\p{Ll}/u', $value) || !preg_match('/[0-9]/', $value) || !preg_match('/[^\p{Lu}\p{Ll}0-9]/u', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return trans('validation.password_strength');
    }
}
