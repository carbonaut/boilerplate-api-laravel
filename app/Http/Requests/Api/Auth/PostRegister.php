<?php

namespace App\Http\Requests\Api\Auth;

use App\Rules\PasswordStrength;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PostRegister extends FormRequest
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
            'first_name'            => 'required|string',
            'last_name'             => 'required|string|different:first_name',
            'email'                 => 'required|email:filter|unique:App\Models\User,email',
            'date_of_birth'         => 'required|date_format:Y-m-d', 'after_or_equal:' . Carbon::now()->subYears(110)->toDateString(),
            'gender'                => 'required|integer|in:1,2,9',
            'password'              => ['required', 'same:password_confirmation', new PasswordStrength()],
            'password_confirmation' => 'required',
            'language_id'           => 'nullable|uuid|exists:App\Models\Language,id',
        ];
    }
}
