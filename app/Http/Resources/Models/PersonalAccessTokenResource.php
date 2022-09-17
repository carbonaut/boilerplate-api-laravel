<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;

class PersonalAccessTokenResource extends BaseResource
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
            'token'      => $this->plainTextToken,
            'expires_at' => $this->accessToken->expires_at,
        ];
    }
}
