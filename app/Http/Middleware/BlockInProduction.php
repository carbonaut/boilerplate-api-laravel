<?php

namespace App\Http\Middleware;

use Closure;

class BlockInProduction {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (config('app.env') === 'production') {
            // TODO: Raise an exception as if the route was not found
            return response(null, 404);
        }

        return $next($request);
    }
}
