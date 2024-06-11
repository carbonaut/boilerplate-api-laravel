<?php

namespace App\Http\Middleware;

use App\Enums\Language;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Localize
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
        // Use the locale of the user account
        if ($request->user() && $request->user() instanceof User) {
            App::setLocale($request->user()->language);
        }
        // If no authenticated user, fallback to the Accept-Language header
        elseif ($request->header('Accept-Language') !== null) {
            // Match the Accept-Language header with the available languages
            $locale = $request->getPreferredLanguage(
                array_column(Language::cases(), 'value')
            );

            if ($locale) {
                App::setLocale($locale);
            }
        }

        return $next($request);
    }
}
