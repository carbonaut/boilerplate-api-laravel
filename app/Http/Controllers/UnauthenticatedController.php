<?php

namespace App\Http\Controllers;

use App\Exceptions\TranslatableException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class UnauthenticatedController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
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
