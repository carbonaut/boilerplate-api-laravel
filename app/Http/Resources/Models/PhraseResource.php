<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;

class PhraseResource extends BaseResource
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
            'key'   => $this->key,
            'value' => $this->value,
        ];
    }
}
