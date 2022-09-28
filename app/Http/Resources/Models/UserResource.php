<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;
use App\Models\User;

class UserResource extends BaseResource
{
    /**
     * Current resource.
     *
     * @var User
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, bool|string>
     */
    public function toArray($request): array
    {
        return [
            'user_id'        => $this->resource->id,
            'name'           => $this->resource->name,
            'email'          => $this->resource->email,
            'language'       => $this->resource->language,
            'email_verified' => $this->resource->email_verified,
        ];
    }
}
