<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class BlockInProduction
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
        if (config('app.env') === 'production') {
            throw new RouteNotFoundException("Route [{$request->route()?->uri()}] not defined.");
        }

        return $next($request);
    }
}
