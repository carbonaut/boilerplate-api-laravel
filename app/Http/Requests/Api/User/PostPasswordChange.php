<?php

namespace App\Http\Requests\Api\User;

use App\Rules\PasswordStrength;
use Illuminate\Foundation\Http\FormRequest;

class PostPasswordChange extends FormRequest
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
            'old_password'              => ['required', 'password'],
            'new_password'              => ['required', 'confirmed', new PasswordStrength()],
        ];
    }
}
