<?php

namespace App\Http\Controllers;

use App\Exceptions\TranslatableException;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class AuthenticatedController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Current authenticated User.
     *
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // For this controller, we require an authenticated user of type User
            if (!$request->user() instanceof User) {
                throw new TranslatableException(
                    500,
                    'Invalid authenticated user type.',
                    'api.ERROR.SOMETHING_WENT_WRONG',
                );
            }

            $this->user = $request->user();

            return $next($request);
        });
    }
}
