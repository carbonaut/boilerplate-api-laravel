<?php

namespace App\Http\Traits;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests as IlluminateAuthorizesRequests;

trait AuthorizesRequests
{
    use IlluminateAuthorizesRequests;

    /**
     * Authorize any of given actions for the current user.
     *
     * @param array|mixed $abilities
     * @param array|mixed $arguments
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function authorizeAny($abilities = [], $arguments = [])
    {
        foreach ($abilities as $ability) {
            try {
                $response = $this->authorize($ability, $arguments);
            } catch (Exception $e) {
                $exception = $e;

                continue;
            }

            return $response;
        }

        throw $exception;
    }
}
