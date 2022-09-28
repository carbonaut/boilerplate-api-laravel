<?php

namespace App\Services;

use App\Enums\Language;
use App\Exceptions\TranslatableException;
use App\Mail\User\EmailVerification;
use App\Mail\User\PasswordReset;
use App\Models\Device;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Axiom\Rules\Lowercase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
     * @param User                  $user
     * @param array<string, string> $input
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
            ],
            'new_password' => [
                'required',
                'string',
                Rules\Password::defaults(),
            ],
        ])->after(function ($validator) use ($user, $input) {
            if (!isset($input['current_password']) || !Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('api.ERROR.CURRENT_PASSWORD_DOES_NOT_MATCH'));
            }
        })->validate();

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
     * @param array<string, string> $input
     *
     * @return User
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createUser(array $input): User
    {
        // Make sure the email is lowercased
        if (isset($input['email'])) {
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
                new Lowercase(),
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

        $user = User::create([
            'name'                               => $validated['name'],
            'email'                              => $validated['email'],
            'password'                           => Hash::make($validated['password']),
            'language'                           => $validated['language'],
            'email_verified_at'                  => null,
            'email_verification_code'            => rand(100000, 999999),
            'email_verification_code_expires_at' => now()->addHour(),
            'remember_token'                     => Str::random(10),
        ]);

        Mail::to($user)->queue(new EmailVerification($user));

        return $user;
    }

    /**
     * Update certain user fields.
     *
     * @param User                  $user
     * @param array<string, string> $input
     *
     * @return User
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function patchUser(User $user, array $input): User
    {
        // Validate input
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
     * @param User                  $user
     * @param string                $uuid
     * @param array<string, string> $input
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
     * @throws TranslatableException
     */
    public function requestEmailVerificationCode(User $user): void
    {
        if ($user->email_verified_at !== null) {
            throw new TranslatableException(
                422,
                'Email already verified.',
                'api.ERROR.EMAIL.ALREADY_VERIFIED'
            );
        }

        $user->update([
            'email_verification_code'            => rand(100000, 999999),
            'email_verification_code_expires_at' => now()->addHour(),
        ]);

        Mail::to($user)->queue(new EmailVerification($user));
    }

    /**
     * Verify the user email.
     *
     * @param User                  $user
     * @param array<string, string> $input
     *
     * @return void
     *
     * @throws TranslatableException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function verifyEmail(User $user, array $input): void
    {
        // Validate input
        $validated = Validator::make($input, [
            'email_verification_code' => ['required', 'integer'],
        ])->validate();

        if ($user->email_verified_at !== null) {
            return;
        }

        // Expired code
        if (now()->gt($user->email_verification_code_expires_at)) {
            $this->requestEmailVerificationCode($user);

            throw new TranslatableException(
                422,
                'Verification code expired. A new code was sent.',
                'api.ERROR.EMAIL.VERIFICATION_CODE_EXPIRED'
            );
        }

        // Code mismatch
        if ($user->email_verification_code !== intval($validated['email_verification_code'])) {
            throw new TranslatableException(
                422,
                'Invalid verification code.',
                'api.ERROR.EMAIL.VERIFICATION_CODE_MISMATCH'
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
     * @param array<string, string> $input
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

        $user = User::firstWhere('email', $validated['email']);

        if ($user === null) {
            return;
        }

        $token = Password::broker()->createToken($user);

        Mail::to($user)->queue(new PasswordReset($user, $token));
    }

    /**
     * Resets the user password.
     *
     * @param array<string, string> $input
     *
     * @return void
     *
     * @throws TranslatableException
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

        $reset = Password::broker()->reset(
            [
                'token'                 => $validated['token'],
                'email'                 => $validated['email'],
                'password'              => $validated['new_password'],
                'password_confirmation' => $validated['new_password'],
            ],
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                $user->tokens()->delete();
            }
        );

        if ($reset !== Password::PASSWORD_RESET) {
            throw new TranslatableException(
                422,
                'Invalid input for resetting the password.',
                'api.ERROR.PASSWORD-RESET.INVALID-INPUT'
            );
        }
    }
}
