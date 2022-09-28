<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;
use Carbon\Carbon;
use Laravel\Sanctum\NewAccessToken;

class NewAccessTokenResource extends BaseResource
{
    /**
     * Current resource.
     *
     * @var NewAccessToken
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, null|Carbon|string>
     */
    public function toArray($request): array
    {
        return [
            'token'      => $this->resource->plainTextToken,
            'expires_at' => $this->resource->accessToken->expires_at,
        ];
    }
}
