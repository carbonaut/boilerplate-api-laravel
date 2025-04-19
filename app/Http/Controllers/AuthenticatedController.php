<?php

namespace App\Http\Controllers;

use App\Exceptions\NormalizedException;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;

class AuthenticatedController extends Controller
{
    use DispatchesJobs;

    /**
     * Current authenticated User.
     *
     * @var User
     */
    protected User $user;

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            // For this controller, we require an authenticated user of type User
            if (!$request->user() instanceof User) {
                throw new NormalizedException(
                    500,
                    'Invalid authenticated user type.',
                    __('api.ERROR.SOMETHING_WENT_WRONG'),
                );
            }

            $this->user = $request->user();

            return $next($request);
        });
    }
}
