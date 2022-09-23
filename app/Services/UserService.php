<?php

namespace App\Services;

use App\Enums\Language;
use App\Mail\User\EmailVerification;
use App\Models\Device;
use App\Models\User;
use Axiom\Rules\Lowercase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserService
{
    /**
     * Change the user password.
     *
     * @param User  $user
     * @param array $input
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
                Password::defaults(),
            ],
        ])->after(function ($validator) use ($user, $input) {
            if (!isset($input['current_password']) || !Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('api.ERROR.CURRENT_PASSWORD_DOES_NOT_MATCH'));
            }
        })->validate();

        // Update password
        $user->password = Hash::make($input['new_password']);
        $user->save();

        // Revoke other tokens
        $user->tokens()->whereNot('id', $user->currentAccessToken()->id)->delete();
    }

    /**
     * Create a new user.
     *
     * @param array $input
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
                Password::defaults(),
            ],
            'language' => [
                'required',
                'string',
                new Enum(Language::class),
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
     * @param User  $user
     * @param array $input
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
                new Enum(Language::class),
            ],
        ])->validate();

        $user->update($validated);

        return $user;
    }

    /**
     * Upsert a user device.
     *
     * @param User   $user
     * @param string $uuid
     * @param array  $input
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
}
