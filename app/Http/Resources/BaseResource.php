<?php

namespace App\Http\Resources;

use App\Interfaces\BaseResourceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property bool $preserveKeys */
class BaseResource extends JsonResource implements BaseResourceInterface
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
     * Create a new anonymous resource collection.
     *
     * Overriden method to allow creating a collection from a custom class instead of the default
     * one, allowing us to tap into the response and remove some fields we don't want. This was
     * copied from the parent class, with the only difference being the class used to create the
     * resource collection.
     *
     * @param mixed $resource
     *
     * @return AnonymousResourceCollection
     */
    public static function collection($resource): AnonymousResourceCollection
    {
        return tap(new BaseResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
