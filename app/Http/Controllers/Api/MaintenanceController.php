<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AuthenticatedController;
use App\Http\Requests\Api\Maintenance\MaintenanceRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends AuthenticatedController
{
    /**
     * Put the application down for maintenance.
     *
     * @param MaintenanceRequest $request
     *
     * @return array<void>
     */
    public function postDown(MaintenanceRequest $request): array
    {
        if (!App::isDownForMaintenance()) {
            Artisan::call('down');
        }

        return [];
    }

    /**
     * Put the application up from maintenance.
     *
     * @param MaintenanceRequest $request
     *
     * @return array<void>
     */
    public function postUp(MaintenanceRequest $request): array
    {
        if (App::isDownForMaintenance()) {
            Artisan::call('up');
        }

        return [];
    }
}
