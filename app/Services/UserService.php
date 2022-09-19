<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
}
