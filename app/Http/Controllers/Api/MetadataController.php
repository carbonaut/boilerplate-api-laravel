<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Phrase;
use App\Support\Helpers;
use Illuminate\Http\Request;

class MetadataController extends Controller
{
    //======================================================================
    // CONSTRUCTOR
    //
    // Current authenticated user is loaded into $this->user by the parent
    // controller class
    //
    //======================================================================
    public function __construct(Request $request)
    {
        parent::__construct();
    }

    //======================================================================
    // ROUTER METHODS
    //======================================================================

    /**
     * Search the languages by name or locale.
     *
     * @param Request $request
     *
     * @return array
     */
    public function getLanguagesSearch(Request $request, string $search_string = null)
    {
        $languages = Language::where('name', 'ilike', "%{$search_string}%")->orWhere('locale', 'ilike', "%{$search_string}%")->orderBy('name')->get();

        return Helpers::recursive_array_only($languages->toArray(), [
            'array.language_id',
            'array.name',
            'array.locale',
        ]);
    }

    /**
     * Search by phrases by locale and type.
     *
     * @param Request $request
     * @param string  $locale
     * @param string  $type
     *
     * return array
     */
    public function getPhrases(Request $request, string $type)
    {
        $request->request->add(['type' => $type]);

        $request->validate([
            'type' => 'required|string',
        ]);

        return Phrase::where('type', $request->type)
            ->orderBy('key')
            ->get()
            ->mapWithKeys(function ($phrase) {
                return [$phrase->key => $phrase->value];
            });
    }
}
