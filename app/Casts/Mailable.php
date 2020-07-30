<?php

namespace App\Casts;

use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable as IlluminateMailable;

class Mailable implements CastsAttributes {
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return array
     */
    public function get($model, $key, $value, $attributes) {
        // Don't unserialize null values
        if ($value === null) {
            return null;
        }

        return unserialize(base64_decode($value));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model  $model
     * @param string $key
     * @param array  $value
     * @param array  $attributes
     *
     * @return string
     */
    public function set($model, $key, $value, $attributes) {
        // Allow this property to be nullable
        if ($value === null) {
            return null;
        }

        // Verify if the property value is an Mailable instance
        if (!$value instanceof IlluminateMailable) {
            throw new Exception('Property value must be an instance of Illuminate\Mail\Mailable.');
        }

        return base64_encode(serialize($value));
    }
}
