<?php

namespace App\Http\Middleware;

use App\Enums\Language;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\App;

class Localize
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
        // Use the locale of the user account
        if ($request->user() && $request->user() instanceof User) {
            App::setLocale($request->user()->language);
        }
        // If no authenticated user, fallback to the Accept-Language header
        elseif ($request->header('Accept-Language') !== null) {
            // Match the Accept-Language header with the available languages
            App::setLocale(
                $request->getPreferredLanguage(
                    array_column(Language::cases(), 'value')
                )
            );
        }

        return $next($request);
    }
}
