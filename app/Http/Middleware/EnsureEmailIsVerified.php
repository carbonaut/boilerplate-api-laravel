<?php

namespace App\Http\Middleware;

use App\Exceptions\TranslatableException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty($request->user()?->email_verified_at)) {
            throw new TranslatableException(
                403,
                'User email must be verified before accessing protected routes.',
                'api.ERROR.EMAIL.NOT_VERIFIED'
            );
        }

        return $next($request);
    }
}
