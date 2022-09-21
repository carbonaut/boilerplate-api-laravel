<?php

namespace App\Services;

use App\Enums\Language;
use App\Models\User;
use Axiom\Rules\Lowercase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

        // // TODO: Send the email verification email
        // $email = new Email();
        // $email->user_id = $user->user_id;
        // $email->mailable = new EmailVerification($user);
        // $email->type = 'email-verification';
        // $email->save();

        return User::create([
            'name'                               => $validated['name'],
            'email'                              => $validated['email'],
            'password'                           => Hash::make($validated['password']),
            'language'                           => $validated['language'],
            'email_verified_at'                  => null,
            'email_verification_code'            => rand(100000, 999999),
            'email_verification_code_expires_at' => now()->addHour(),
        ]);
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
}
