<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class PostUserDevices extends FormRequest
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
            'manufacturer' => 'required|string|max:255',
            'model'        => 'required|string|max:255',
            'version'      => 'required|string|max:255',
            'platform'     => 'required|string|max:255',
            'uuid'         => 'required|string|max:255',
            'is_virtual'   => 'required|boolean',
            'app_version'  => 'string|max:255',
            'push_token'   => 'string|min:64|max:255',
            'serial'       => 'string|max:255',
        ];
    }
}
