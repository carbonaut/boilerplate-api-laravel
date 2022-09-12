<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ApiController extends Controller
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
