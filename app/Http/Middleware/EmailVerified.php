<?php

namespace App\Http\Middleware;

use App\Models\Phrase;
use App\Models\User;
use Closure;

class EmailVerified {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->user() && $request->user() instanceof User && $request->user()->email_verified_at === null) {
            return response()->json([
                'error'   => 'User email not verified.',
                'message' => Phrase::getPhrase('ERROR_EMAIL_NOT_VERIFIED', 'api'),
            ], 500);
        }

        return $next($request);
    }
}
