<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Returns the swagger UI.
     *
     * @return view
     */
    public function getDocumentation()
    {
        if (App::environment() === 'production') {
            abort(404);
        }

        return view('api.layouts.main');
    }

    /**
     * Returns API documentation.
     *
     * @return view
     */
    public function getDocs()
    {
        if (App::environment() === 'production') {
            abort(404);
        }

        if (config('app.env') == 'local') {
            $host = 'api.localhost:8000';
        } else {
            $host = 'api.' . config('app.domain');
        }

        return view('api.openapi')->with([
            'host' => $host,
        ]);
    }

    /**
     * Returns API status.
     *
     * @return array
     */
    public function getStatus()
    {
        return [];
    }
}
