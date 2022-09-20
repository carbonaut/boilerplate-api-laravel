<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;

class UserResource extends BaseResource
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
            'user_id'        => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'language'       => $this->language,
            'email_verified' => $this->email_verified,
        ];
    }
}
