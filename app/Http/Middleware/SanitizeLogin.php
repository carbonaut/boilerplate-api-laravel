<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class SanitizeLogin
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
        // Lowercase email for proper login
        if ($request->username !== null && is_string($request->username)) {
            $request->request->set('username', Str::lower($request->username));
        }

        // Set proper oauth attribute for requesting token
        $request->request->add([
            'grant_type' => 'password',
        ]);

        return $next($request);
    }
}
