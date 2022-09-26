<?php

namespace App\Http\Controllers\Api;

use App\Enums\Language;
use App\Enums\LanguageLineGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resources\GetLanguageLinesByGroupRequest;
use App\Http\Resources\Models\LanguageLineResource;
use App\Http\Resources\Models\LanguageResource;
use App\Models\LanguageLine;
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

    /**
     * Return all language lines from the specified group.
     *
     * @param GetLanguageLinesByGroupRequest $request
     * @param LanguageLineGroup              $group
     *
     * @return ResourceCollection
     */
    public function getLanguageLinesByGroup(GetLanguageLinesByGroupRequest $request, LanguageLineGroup $group): ResourceCollection
    {
        $languageLines = LanguageLine::query()
            ->where('group', $group->value)
            ->orderBy('key')
            ->get();

        return LanguageLineResource::collection(
            $languageLines
        );
    }
}
