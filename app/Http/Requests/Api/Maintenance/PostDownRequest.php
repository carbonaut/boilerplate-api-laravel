<?php

namespace App\Http\Requests\Api\Maintenance;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PostDownRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() ?
            $this->user()->can('maintenance', User::class) : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Rule|string>>
     */
    public function rules()
    {
        return [];
    }
}
