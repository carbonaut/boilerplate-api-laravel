<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
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

        $this->middleware(function ($request, $next) {
            $this->authorize('maintenance', $this->user);

            return $next($request);
        });
    }

    //======================================================================
    // ROUTER METHODS
    //======================================================================

    /**
     * Puts the api under maintenance mode.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postEnable(Request $request)
    {
        if (!App::isDownForMaintenance()) {
            Artisan::call('down');
        }

        return [];
    }

    /**
     * Removes the api from maintenance mode.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postDisable(Request $request)
    {
        if (App::isDownForMaintenance()) {
            Artisan::call('up');
        }

        return [];
    }
}
