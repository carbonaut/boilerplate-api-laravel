<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\StandardException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PostLogin;
use App\Http\Resources\Models\DeviceResource;
use App\Http\Resources\Models\PersonalAccessTokenResource;
use App\Http\Resources\Models\UserResource;
use App\Http\Requests\Api\Auth\PostEmailVerificationConfirm;
use App\Http\Requests\Api\Auth\PostEmailVerificationRequest;
use App\Http\Requests\Api\Auth\PostPasswordResetRequest;
use App\Http\Requests\Api\Auth\PostPasswordResetSubmit;
use App\Http\Requests\Api\Auth\PostRegister;
use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use App\Models\Email;
use App\Models\Phrase;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\Helpers;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Authenticate a user.
     *
     * @param PostLogin $request
     *
     * @return PersonalAccessTokenResource
     *
     * @throws AuthenticationException
     */
    public function postLogin(PostLogin $request): PersonalAccessTokenResource
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            throw new StandardException(
                401,
                'Invalid credentials.',
                __('api.ERROR.AUTH.INVALID_CREDENTIALS')
            );
        }

        return new PersonalAccessTokenResource(
            User::findOrFail(Auth::user()->id)->createToken('api')
        );
    }

    /**
     * Creates a new user.
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
     * @return array
     */
    public function putUserDevice(Request $request, UserService $userService, string $uuid): DeviceResource
    {
        $device = $userService->upsertUserDevice($this->user, $uuid, $request->all());

        return new DeviceResource($device);
    }

    /**
     * Revoke the current user access token.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postLogout(Request $request): array
    {
        $this->user->currentAccessToken()->delete();

        return [];
    }

    /**
     * Revoke all user access tokens.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postLogoutAll(Request $request): array
    {
        $this->user->tokens()->delete();

        return [];
    }

    /**
     * Changes to a new user password given the current password.
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return array
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
     * @return array
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
     * @return array
     */
    public function postEmailVerification(Request $request, UserService $userService): array
    {
        $userService->verifyEmail($this->user, $request->all());

        return [];
    }

    /**
     * Sends a password reset token by email to the user.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postPasswordResetRequest(PostPasswordResetRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $token = Password::broker()->createToken($user);

        // Send the password reset email
        $email = new Email();
        $email->user_id = $user->user_id;
        $email->mailable = new PasswordReset($user, $token);
        $email->type = 'password-reset';
        $email->save();

        return [];
    }

    /**
     * Resets the user password if token is valid.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postPasswordResetSubmit(PostPasswordResetSubmit $request)
    {
        $request->validate();

        // Response is only sent on success, otherwise exception is thrown
        $response = $this->reset($request);

        // Since reset was successful, invalidate all previous tokens
        $tokens = User::where('email', $request->email)->first()->tokens;

        foreach ($tokens as $token) {
            User::revokeToken($token);
        }

        return $response;
    }
}
