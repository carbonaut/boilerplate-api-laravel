<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\UnauthenticatedController;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ApiController extends UnauthenticatedController
{
    /**
     * Returns the swagger UI.
     *
     * @return View
     */
    public function getApi(Request $request): View
    {
        return view('api.swagger.ui');
    }

    /**
     * Returns API documentation.
     *
     * @return string
     */
    public function getApiDocumentation(Request $request): string
    {
        return File::get(resource_path() . '/api/documentation.yaml');
    }
}
