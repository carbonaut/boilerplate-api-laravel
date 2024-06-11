<?php

namespace App\Http\Controllers\Api;

use App\Enums\Language;
use App\Enums\LanguageLineGroup;
use App\Http\Controllers\UnauthenticatedController;
use App\Http\Requests\Api\Resources\GetLanguageLinesRequest;
use App\Http\Resources\Models\LanguageLineResource;
use App\Http\Resources\Models\LanguageResource;
use App\Models\LanguageLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResourcesController extends UnauthenticatedController
{
    /**
     * Get the available languages.
     *
     * @param Request $request
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
     * Get all language lines from the specified group.
     *
     * @param GetLanguageLinesRequest $request
     * @param LanguageLineGroup       $group
     *
     * @return ResourceCollection
     */
    public function getLanguageLines(GetLanguageLinesRequest $request, LanguageLineGroup $group): ResourceCollection
    {
        $languageLines = LanguageLine::query()
            ->where('group', $group->value)
            ->orderBy('key')
            ->get()
        ;

        return LanguageLineResource::collection(
            $languageLines
        );
    }
}
