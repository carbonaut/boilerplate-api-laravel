<?php

namespace App\Http\Middleware;

use Closure;

class SanitizeRefresh {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        // Set proper oauth attribute for refreshing token
        $request->request->add([
            'grant_type' => 'refresh_token',
        ]);

        return $next($request);
    }
}
