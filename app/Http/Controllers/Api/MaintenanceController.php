<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AuthenticatedController;
use App\Http\Requests\Api\Maintenance\PostDownRequest;
use App\Http\Requests\Api\Maintenance\PostUpRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends AuthenticatedController
{
    /**
     * Puts the application down for maintenance.
     *
     * @param PostDownRequest $request
     *
     * @return array<void>
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
     * @param PostUpRequest $request
     *
     * @return array<void>
     */
    public function postUp(PostUpRequest $request): array
    {
        if (App::isDownForMaintenance()) {
            Artisan::call('up');
        }

        return [];
    }
}
