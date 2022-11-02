<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class BlockInProduction
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure                  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('app.env') === 'production') {
            throw new RouteNotFoundException("Route [{$request->route()?->uri()}] not defined.");
        }

        return $next($request);
    }
}
