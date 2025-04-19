<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\NormalizedException;
use App\Http\Controllers\UnauthenticatedController;
use App\Http\Requests\Api\Auth\PostLoginRequest;
use App\Http\Resources\Models\NewAccessTokenResource;
use App\Http\Resources\Models\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends UnauthenticatedController
{
    /**
     * Authenticate a user.
     *
     * @param PostLoginRequest $request
     *
     * @return NewAccessTokenResource
     *
     * @throws NormalizedException
     */
    public function postLogin(PostLoginRequest $request): NewAccessTokenResource
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            throw new NormalizedException(
                401,
                'Invalid credentials.',
                __('api.ERROR.AUTH.INVALID_CREDENTIALS')
            );
        }

        return new NewAccessTokenResource(
            User::findOrFail(Auth::user()?->id)->createToken('api')
        );
    }

    /**
     * Create a new user.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return UserResource
     */
    public function postRegister(Request $request, UserService $userService): UserResource
    {
        $user = $userService->createUser($request->all());

        return new UserResource($user);
    }

    /**
     * Send a password reset token by email to the user.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return array<void>
     */
    public function postPasswordResetRequest(Request $request, UserService $userService): array
    {
        $userService->requestPasswordResetToken($request->all());

        return [];
    }

    /**
     * Reset the user password if token is valid.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return array<void>
     */
    public function postPasswordResetSubmit(Request $request, UserService $userService): array
    {
        $userService->resetPassword($request->all());

        return [];
    }
}
