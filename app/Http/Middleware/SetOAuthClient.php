<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class SetOAuthClient
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get proper oauth_client based on secret provided
        $oauth_client = DB::table('oauth_clients')->where('id', $request->client)->first();

        if ($oauth_client !== null) {
            $request->request->add([
                'scope'         => '*',
                'client_id'     => $oauth_client->id,
                'client_secret' => $oauth_client->secret,
            ]);
        }

        return $next($request);
    }
}
