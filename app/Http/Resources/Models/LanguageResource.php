<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;

class LanguageResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
        ];
    }
}
