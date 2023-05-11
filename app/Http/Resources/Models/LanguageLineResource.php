<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\BaseResource;
use App\Models\LanguageLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

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
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key'  => $this->resource->key,
            'text' => $this->resource->getTranslation(App::getLocale()),
        ];
    }
}
