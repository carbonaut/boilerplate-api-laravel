<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Localize {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param null|string              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        // Handle only requests with user
        if ($request->user() && $request->user() instanceof User) {
            // Use the locale of the user language
            $request->user()->setLocale();
        }
        // Use the Accept-Language header
        elseif ($request->header('Accept-Language') !== null) {
            App::setLocale(Str::lower(explode(',', $request->header('Accept-Language'))[0]));
        }

        return $next($request);
    }
}
