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

    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user() instanceof User) {
                $this->user = $request->user();
            }

            return $next($request);
        });
    }
}
