<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PostLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:filter',
                'lowercase',
            ],
            'password' => [
                'required',
            ],
        ];
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param null|array<int, null|int|string>|mixed $keys
     *
     * @return array<int|string,mixed>
     */
    public function all($keys = null)
    {
        $email = $this->get('email');

        if (is_string($email)) {
            return array_merge(
                parent::all($keys),
                ['email' => strtolower($email)]
            );
        }

        return parent::all($keys);
    }
}
