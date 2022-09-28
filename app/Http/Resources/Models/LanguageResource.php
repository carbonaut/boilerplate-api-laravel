<?php

namespace App\Http\Resources\Models;

use App\Enums\Language;
use App\Http\Resources\BaseResource;

class LanguageResource extends BaseResource
{
    /**
     * Current resource.
     *
     * @var Language
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, null|string>
     */
    public function toArray($request): array
    {
        return [
            'value' => $this->resource->value,
            'label' => $this->resource->label(),
        ];
    }
}
