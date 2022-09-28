<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\AuthenticatedController;
use App\Http\Resources\Models\DeviceResource;
use App\Http\Resources\Models\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class PrivateController extends AuthenticatedController
{
    /**
     * Returns the authenticated user information.
     *
     * @param Request $request
     *
     * @return UserResource
     */
    public function getUser(Request $request): UserResource
    {
        return new UserResource($this->user);
    }

    /**
     * Update user attributes.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return UserResource
     */
    public function patchUser(Request $request, UserService $userService): UserResource
    {
        $user = $userService->patchUser($this->user, $request->all());

        return new UserResource($user);
    }

    /**
     * Upsert a user device.
     *
     * @param Request     $request
     * @param UserService $userService
     * @param string      $uuid
     *
     * @return DeviceResource
     */
    public function putUserDevice(Request $request, UserService $userService, string $uuid): DeviceResource
    {
        $device = $userService->upsertUserDevice($this->user, $uuid, $request->all());

        return new DeviceResource($device);
    }

    /**
     * Revoke the current user access token.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return array<void>
     */
    public function postLogout(Request $request, UserService $userService): array
    {
        $userService->revokeCurrentAccessToken($this->user);

        return [];
    }

    /**
     * Revoke all user access tokens.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return array<void>
     */
    public function postLogoutAll(Request $request, UserService $userService): array
    {
        $userService->revokeAllAccessTokens($this->user);

        return [];
    }

    /**
     * Changes to a new user password given the current password.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return array<void>
     */
    public function postPasswordChange(Request $request, UserService $userService): array
    {
        $userService->changePassword($this->user, $request->all());

        return [];
    }

    /**
     * Requests an email verification code to be sent to the requested email.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return array<void>
     */
    public function getEmailVerification(Request $request, UserService $userService): array
    {
        $userService->requestEmailVerificationCode($this->user);

        return [];
    }

    /**
     * Verifies an email by the verification code.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return array<void>
     */
    public function postEmailVerification(Request $request, UserService $userService): array
    {
        $userService->verifyEmail($this->user, $request->all());

        return [];
    }
}
