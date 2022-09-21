<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;

class DeviceResource extends BaseResource
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
            'device_id'        => $this->id,
            'uuid'             => $this->uuid,
            'name'             => $this->name,
            'platform'         => $this->platform,
            'operating_system' => $this->operating_system,
            'os_version'       => $this->os_version,
            'manufacturer'     => $this->manufacturer,
            'model'            => $this->model,
            'web_view_version' => $this->web_view_version,
            'app_version'      => $this->app_version,
            'is_virtual'       => $this->is_virtual,
            'push_token'       => $this->push_token,
            'is_active'        => $this->is_active,
        ];
    }
}
