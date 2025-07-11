<?php

namespace App\Services;

use App\Enums\Language;
use App\Exceptions\NormalizedException;
use App\Models\Device;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Notifications\User\EmailVerification;
use App\Notifications\User\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UserService
{
    /**
     * Revoke the current user access token, logging the user out from the current device.
     *
     * @param User $user
     *
     * @return void
     */
    public function revokeCurrentAccessToken(User $user): void
    {
        /**
         * @var PersonalAccessToken $currentAccessToken
         */
        $currentAccessToken = $user->currentAccessToken();

        $currentAccessToken->delete();
    }

    /**
     * Revoke all user access tokens, logging the user out from all devices.
     *
     * @param User $user
     *
     * @return void
     */
    public function revokeAllAccessTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Change the user password.
     *
     * @param User         $user
     * @param array<mixed> $input
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changePassword(User $user, array $input): void
    {
        // Validate input
        Validator::make($input, [
            'current_password' => [
                'required',
                'string',
                'current_password',
            ],
            'new_password' => [
                'required',
                'string',
                Rules\Password::defaults(),
            ],
        ])->validate();

        assert(is_string($input['new_password']));

        // Update password
        $user->password = Hash::make($input['new_password']);
        $user->save();

        /**
         * @var PersonalAccessToken $currentAccessToken
         */
        $currentAccessToken = $user->currentAccessToken();

        // Revoke other tokens
        $user->tokens()->whereNot('id', $currentAccessToken->id)->delete();
    }

    /**
     * Create a new user.
     *
     * @param array<mixed> $input
     *
     * @return User
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createUser(array $input): User
    {
        // Make sure the email is lowercased
        if (isset($input['email']) && is_string($input['email'])) {
            $input['email'] = strtolower($input['email']);
        }

        // Validate input
        $validated = Validator::make($input, [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email:filter',
                'max:255',
                'lowercase',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                Rules\Password::defaults(),
            ],
            'language' => [
                'required',
                'string',
                new Rules\Enum(Language::class),
            ],
        ])->validate();

        assert(is_string($validated['email']));
        assert(is_string($validated['password']));

        $user = User::create([
            'name'                               => $validated['name'],
            'email'                              => strtolower($validated['email']),
            'password'                           => Hash::make($validated['password']),
            'language'                           => $validated['language'],
            'email_verified_at'                  => null,
            'email_verification_code'            => rand(100000, 999999),
            'email_verification_code_expires_at' => now()->addHour(),
            'remember_token'                     => Str::random(10),
        ]);

        $user->notify(new EmailVerification());

        return $user;
    }

    /**
     * Update certain user fields.
     *
     * @param User         $user
     * @param array<mixed> $input
     *
     * @return User
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function patchUser(User $user, array $input): User
    {
        // Validate input
        /** @var array<string, mixed> $validated */
        $validated = Validator::make($input, [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'language' => [
                'sometimes',
                'required',
                'string',
                new Rules\Enum(Language::class),
            ],
        ])->validate();

        $user->update($validated);

        return $user;
    }

    /**
     * Upsert a user device.
     *
     * @param User         $user
     * @param string       $uuid
     * @param array<mixed> $input
     *
     * @return Device
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function upsertUserDevice(User $user, string $uuid, array $input): Device
    {
        // Validate input
        $validated = Validator::make($input, [
            'name'             => ['present', 'nullable', 'string', 'max:255'],
            'platform'         => ['required', 'string', 'max:255'],
            'operating_system' => ['required', 'string', 'max:255'],
            'os_version'       => ['required', 'string', 'max:255'],
            'manufacturer'     => ['required', 'string', 'max:255'],
            'model'            => ['required', 'string', 'max:255'],
            'web_view_version' => ['present', 'nullable', 'string', 'max:255'],
            'app_version'      => ['present', 'nullable', 'string', 'max:255'],
            'is_virtual'       => ['present', 'nullable', 'boolean'],
            'push_token'       => ['present', 'nullable', 'string', 'max:255'],
        ])->validate();

        // Assuming that only one user can be logged in at the same time on a device, this
        // will ensure that pushes will be sent to the user who last used the device.
        Device::where('uuid', $uuid)->update([
            'is_active' => false,
        ]);

        return $user->devices()->updateOrCreate(['uuid' => $uuid], [
            'name'             => $validated['name'],
            'platform'         => $validated['platform'],
            'operating_system' => $validated['operating_system'],
            'os_version'       => $validated['os_version'],
            'manufacturer'     => $validated['manufacturer'],
            'model'            => $validated['model'],
            'web_view_version' => $validated['web_view_version'],
            'app_version'      => $validated['app_version'],
            'is_virtual'       => $validated['is_virtual'],
            'push_token'       => $validated['push_token'],
            'is_active'        => true,
        ]);
    }

    /**
     * Request user email verification code.
     *
     * @param User $user
     *
     * @return void
     *
     * @throws NormalizedException
     */
    public function requestEmailVerificationCode(User $user): void
    {
        if ($user->email_verified_at !== null) {
            throw new NormalizedException(
                422,
                'Email already verified.',
                __('api.ERROR.EMAIL.ALREADY_VERIFIED')
            );
        }

        $user->update([
            'email_verification_code'            => rand(100000, 999999),
            'email_verification_code_expires_at' => now()->addHour(),
        ]);

        $user->notify(new EmailVerification());
    }

    /**
     * Verify the user email.
     *
     * @param User         $user
     * @param array<mixed> $input
     *
     * @return void
     *
     * @throws NormalizedException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function verifyEmail(User $user, array $input): void
    {
        // Validate input
        $validated = Validator::make($input, [
            'email_verification_code' => ['required', 'integer'],
        ])->validate();

        assert(is_int($validated['email_verification_code']));

        if ($user->email_verified_at !== null) {
            throw new NormalizedException(
                422,
                'Email already verified.',
                __('api.ERROR.EMAIL.ALREADY_VERIFIED')
            );
        }

        // Expired code
        if (is_null($user->email_verification_code_expires_at) || $user->email_verification_code_expires_at->isPast()) {
            $this->requestEmailVerificationCode($user);

            throw new NormalizedException(
                422,
                'Verification code expired. A new code was sent.',
                __('api.ERROR.EMAIL.VERIFICATION_CODE_EXPIRED')
            );
        }

        // Code mismatch
        if ($user->email_verification_code !== intval($validated['email_verification_code'])) {
            throw new NormalizedException(
                422,
                'Invalid verification code.',
                __('api.ERROR.EMAIL.VERIFICATION_CODE_MISMATCH')
            );
        }

        $user->update([
            'email_verification_code'            => null,
            'email_verification_code_expires_at' => null,
            'email_verified_at'                  => now(),
        ]);
    }

    /**
     * Sends a password-reset token to the user email.
     *
     * @param array<mixed> $input
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function requestPasswordResetToken(array $input): void
    {
        // Validate input
        $validated = Validator::make($input, [
            'email' => ['required', 'email:filter'],
        ])->validate();

        assert(is_string($validated['email']));

        $user = User::firstWhere('email', strtolower($validated['email']));

        if ($user === null) {
            return;
        }

        $passwordBroker = Password::broker();
        assert($passwordBroker instanceof PasswordBroker);

        $user->notify(
            new PasswordReset($passwordBroker->createToken($user))
        );
    }

    /**
     * Resets the user password.
     *
     * @param array<mixed> $input
     *
     * @return void
     *
     * @throws NormalizedException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function resetPassword(array $input): void
    {
        // Validate input
        $validated = Validator::make($input, [
            'token'        => ['required'],
            'email'        => ['required', 'email:filter'],
            'new_password' => ['required', 'string', Rules\Password::defaults()],
        ])->validate();

        assert(is_string($validated['email']));

        $reset = Password::broker()->reset(
            [
                'token'                 => $validated['token'],
                'email'                 => strtolower($validated['email']),
                'password'              => $validated['new_password'],
                'password_confirmation' => $validated['new_password'],
            ],
            function (User $user, string $password): void {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                $user->tokens()->delete();
            }
        );

        if ($reset !== Password::PASSWORD_RESET) {
            throw new NormalizedException(
                422,
                'Invalid input for resetting the password.',
                __('api.ERROR.PASSWORD-RESET.INVALID-INPUT')
            );
        }
    }
}
