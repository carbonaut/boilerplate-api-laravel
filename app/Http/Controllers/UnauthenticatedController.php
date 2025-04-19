<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;

class UnauthenticatedController extends Controller
{
    use DispatchesJobs;

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            // Validate if there is no user authenticated
            if (!is_null($request->user())) {
                throw new TranslatableException(
                    500,
                    'Authenticated user in unauthenticated route.',
                    'api.ERROR.SOMETHING_WENT_WRONG',
                );
            }

            return $next($request);
        });
    }
}
