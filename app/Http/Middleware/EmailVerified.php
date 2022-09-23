<?php

namespace App\Http\Middleware;

use App\Exceptions\StandardException;
use Closure;

class EmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (empty($request->user()?->email_verified_at)) {
            throw new StandardException(
                422,
                'User email must be verified before accessing protected routes.',
                __('api.ERROR.EMAIL.NOT_VERIFIED')
            );
        }

        return $next($request);
    }
}
