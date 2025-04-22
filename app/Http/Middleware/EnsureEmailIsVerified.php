<?php

namespace App\Http\Middleware;

use App\Exceptions\NormalizedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request                                                        $request
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (is_null($request->user()?->email_verified_at)) {
            throw new NormalizedException(
                403,
                'User email must be verified before accessing protected routes.',
                __('api.ERROR.EMAIL.NOT_VERIFIED')
            );
        }

        return $next($request);
    }
}
