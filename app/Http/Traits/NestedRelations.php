<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

trait NestedRelations {
    /**
     * If this is a one-to-N relation, it will only check the first object
     * When added as a trait in the model, it overrides the original relationLoaded.
     *
     * @param array|string $relations
     *
     * @return bool
     */
    public function relationLoaded($relations) {
        // Get arguments into array
        $relations = is_string($relations) ? func_get_args() : $relations;

        // Build multidimensional array from dot notation
        $nested_relations = [];

        foreach ($relations as $relation) {
            Arr::set($nested_relations, $relation, true);
        }

        /*
         * Iterate nested relation
         * Check if relation is loaded
         * If requested relation is array, request status from related model
         */
        foreach ($nested_relations as $key => $value) {
            // Return false if relation doesn't exist
            if (!parent::relationLoaded($key)) {
                return false;
            }

            // If relation is an array, must check deeper
            if (is_array($value)) {
                // If relation is a collection, must check if loaded on all objects
                if ($this->{$key} instanceof Collection) {
                    // To avoid iterating on all to-many relation, check if loaded only on the first model
                    if (!$this->{$key}->isEmpty()) {
                        if (!$this->{$key}->first()->relationLoaded(array_keys(Arr::dot($value)))) {
                            return false;
                        }
                    }
                } else {
                    if ($this->{$key} !== null && !$this->{$key}->relationLoaded(array_keys(Arr::dot($value)))) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
