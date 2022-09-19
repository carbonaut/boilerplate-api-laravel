<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Maintenance\PostDownRequest;
use App\Http\Requests\Api\Maintenance\PostUpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    /**
     * Puts the application down for maintenance.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postDown(PostDownRequest $request): array
    {
        if (!App::isDownForMaintenance()) {
            Artisan::call('down');
        }

        return [];
    }

    /**
     * Puts the application up from maintenance.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postUp(PostUpRequest $request): array
    {
        if (App::isDownForMaintenance()) {
            Artisan::call('up');
        }

        return [];
    }
}
