<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use Artisan;
use Illuminate\Http\Request;

class MaintenanceController extends Controller {
    //======================================================================
    // CONSTRUCTOR
    //
    // Current authenticated user is loaded into $this->user by the parent
    // controller class
    //
    //======================================================================
    public function __construct(Request $request) {
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
    public function postEnable(Request $request) {
        $request->validate([
            'message' => 'nullable|string',
            'allow'   => 'nullable|ip',
        ]);

        if (App::isDownForMaintenance()) {
            Artisan::call('up');
        }

        $command = 'down';

        if ($request->message !== null) {
            $command .= " --message=\"{$request->message}\"";
        }

        if ($request->allow !== null) {
            $command .= " --allow={$request->allow}";
        }

        Artisan::call($command);

        return [];
    }

    /**
     * Removes the api from maintenance mode.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postDisable(Request $request) {
        if (App::isDownForMaintenance()) {
            Artisan::call('up');
        }

        return [];
    }
}
