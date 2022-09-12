<?php

namespace App\Http\Controllers\Api;

use App\Enums\Language;
use App\Http\Controllers\Controller;
use App\Http\Resources\Models\LanguageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResourcesController extends Controller
{
    /**
     * Returns the available languages.
     *
     * @return ResourceCollection
     */
    public function getLanguages(Request $request): ResourceCollection
    {
        return LanguageResource::collection(
            Language::cases()
        );
    }
}
