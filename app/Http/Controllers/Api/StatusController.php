<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Returns an empty 200 response.
     *
     * @return array
     */
    public function getStatus(Request $request): array
    {
        return [];
    }
}
