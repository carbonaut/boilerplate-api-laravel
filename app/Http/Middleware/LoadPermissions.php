<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class LoadPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param null|string              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // Handle only requests with user
        if ($request->user() && $request->user() instanceof User) {
            // Load the user's profile and profile permissions if isn't already loaded
            if (!$request->user()->relationLoaded(['profile', 'profile.permissions'])) {
                $request->user()->load(['profile', 'profile.permissions']);
            }
        }

        return $next($request);
    }
}
