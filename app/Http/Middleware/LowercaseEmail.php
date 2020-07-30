<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class LowercaseEmail {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->email !== null && is_string($request->email)) {
            $request->request->set('email', Str::lower($request->email));
        }

        return $next($request);
    }
}
