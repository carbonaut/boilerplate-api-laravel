<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\UnauthenticatedController;
use Illuminate\Http\Request;

class StatusController extends UnauthenticatedController
{
    /**
     * Get an empty successful response.
     *
     * @param Request $request
     *
     * @return array<void>
     */
    public function getStatus(Request $request): array
    {
        return [];
    }
}
