<?php

namespace App\Http\Requests\Api\Auth;

use App\Rules\PasswordStrength;
use Illuminate\Foundation\Http\FormRequest;

class PostPasswordResetSubmit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token'                 => 'required',
            'email'                 => 'required|email:filter|exists:App\Models\User,email',
            'password'              => ['required', 'same:password_confirmation', new PasswordStrength()],
            'password_confirmation' => 'required',
        ];
    }
}
