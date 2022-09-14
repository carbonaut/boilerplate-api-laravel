<?php

namespace App\Http\Controllers\Api;

use App\Enums\Language;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resources\GetPhrasesByTypeRequest;
use App\Http\Resources\Models\LanguageResource;
use App\Http\Resources\Models\PhraseResource;
use App\Models\Phrase;
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
     * Return all phrases from the specified type.
     *
     * @param GetPhrasesByTypeRequest $request
     * @param string                  $type
     *
     * @return ResourceCollection
     */
    public function getPhrasesByType(GetPhrasesByTypeRequest $request, string $type): ResourceCollection
    {
        $phrases = Phrase::query()
            ->where('type', $type)
            ->orderBy('key')
            ->get();

        return PhraseResource::collection(
            $phrases
        );
    }
}
