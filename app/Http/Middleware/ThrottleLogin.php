<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

class ThrottleLogin {
    use ThrottlesLogins;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        // BEFORE request

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Successful or not, we must increment the number of attempts to login. When
        // this user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        // Login request
        $response = $next($request);

        // AFTER request

        // In case login is successful, we clear the login attemps, just in case
        // the user logs out and tries to login again.
        if ($response->status() == 200 & property_exists(json_decode($response->getContent()), 'access_token')) {
            $this->clearLoginAttempts($request);
        }

        return $response;
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function username() {
        return 'username';
    }
}
