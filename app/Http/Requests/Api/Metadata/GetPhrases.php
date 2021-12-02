<?php

namespace App\Http\Requests\Api\Metadata;

use Illuminate\Foundation\Http\FormRequest;

class GetPhrases extends FormRequest
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
            'type' => 'required|string|exists:App\Models\Phrase,type',
        ];
    }

    /**
     * Inject route parameters into the Form Request for validation.
     *
     * @param null|array|mixed $keys
     *
     * @return array
     */
    public function all($keys = null)
    {
        // Add route parameters to validation data
        return array_merge(parent::all(), [
            'type' => $this->route('type'),
        ]);
    }
}
