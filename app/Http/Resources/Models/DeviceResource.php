<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceResource extends BaseResource
{
    /**
     * Current resource.
     *
     * @var Device
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, null|bool|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'device_id'        => $this->resource->id,
            'uuid'             => $this->resource->uuid,
            'name'             => $this->resource->name,
            'platform'         => $this->resource->platform,
            'operating_system' => $this->resource->operating_system,
            'os_version'       => $this->resource->os_version,
            'manufacturer'     => $this->resource->manufacturer,
            'model'            => $this->resource->model,
            'web_view_version' => $this->resource->web_view_version,
            'app_version'      => $this->resource->app_version,
            'is_virtual'       => $this->resource->is_virtual,
            'push_token'       => $this->resource->push_token,
            'is_active'        => $this->resource->is_active,
        ];
    }
}
