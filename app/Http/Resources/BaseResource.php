<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * Set to null on a base class to prevent wrapping individual resources.
     *
     * @var null|string
     */
    public static $wrap;

    /**
     * Create a new resource collection instance.
     *
     * Overriden method to replace the default collection class.
     *
     * @param mixed $resource
     *
     * @return BaseResourceCollection
     */
    protected static function newCollection($resource)
    {
        return new BaseResourceCollection($resource, static::class);
    }
}
