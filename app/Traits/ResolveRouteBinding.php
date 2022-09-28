<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * This trait should be used in Laravel models to override the existing
 * resolveRouteBinding method, so it can properly parse the string or
 * integer before passing it to the model search query.
 */
trait ResolveRouteBinding
{
    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed       $value
     * @param null|string $field
     *
     * @return null|Model
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if (($field ?? $this->getRouteKeyName()) === 'id') {
            switch ($this->keyType) {
                case 'string':
                    $value = Str::isUuid(strval($value)) ? $value : null;

                    break;
                case 'int':
                case 'integer':
                    $value = filter_var($value, FILTER_VALIDATE_INT) ? $value : null;

                    break;
            }
        }

        return parent::resolveRouteBinding($value, $field);
    }
}
