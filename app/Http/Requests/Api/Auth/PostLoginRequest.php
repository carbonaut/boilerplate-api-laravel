<?php

namespace App\Http\Requests\Api\Auth;

use Axiom\Rules\Lowercase;
use Illuminate\Contracts\Validation\Rule;
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
     * @return array<string, array<Rule|string>>
     */
    public function rules(): array
    {
        return [
            'email'    => [
                'required',
                'email:filter',
                new Lowercase(),
            ],
            'password' => [
                'required',
            ],
        ];
    }

    /**
     * Tap into the parameters to make sure the email is lowercased.
     *
     * @param null|array|mixed $keys
     *
     * @return array<string, string>
     */
    public function all($keys = null): array
    {
        if ($this->get('email')) {
            return array_merge(
                parent::all(),
                [
                    'email' => strtolower(strval($this->get('email'))),
                ]
            );
        }

        return parent::all();
    }
}
