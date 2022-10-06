<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;
use App\Models\LanguageLine;

class LanguageLineResource extends BaseResource
{
    /**
     * Current resource.
     *
     * @var LanguageLine
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, string>
     */
    public function toArray($request): array
    {
        return [
            'key'  => $this->resource->key,
            'text' => strval(__($this->resource->handle)),
        ];
    }
}
