<?php

namespace App\Http\Controllers;

use App\Http\Traits\AuthorizesRequests;
use App\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Current authenticated User.
     *
     * @var null|User
     */
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // If an authenticated User is present, store it in the $user property.
            if ($request->user() instanceof User) {
                $this->user = $request->user();
            }

            return $next($request);
        });
    }
}
