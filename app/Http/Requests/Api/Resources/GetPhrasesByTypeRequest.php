<?php

namespace App\Http\Requests\Api\Resources;

use App\Enums\PhraseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetPhrasesByTypeRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'type' => [
                'required',
                'string',
                new Enum(PhraseType::class),
            ],
        ];
    }

    /**
     * Inject route parameters into the Form Request for validation.
     *
     * @param null|array|mixed $keys
     *
     * @return array
     */
    public function all($keys = null): array
    {
        // Add route parameters to validation data
        return array_merge(
            parent::all(),
            [
                'type' => $this->route('type'),
            ]
        );
    }
}
