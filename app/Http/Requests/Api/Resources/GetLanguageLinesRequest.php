<?php

namespace App\Http\Requests\Api\Resources;

use App\Enums\LanguageLineGroup;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetLanguageLinesRequest extends FormRequest
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
     * @return array<string, array<Rule|string>>
     */
    public function rules()
    {
        return [
            'group' => [
                'required',
                new Enum(LanguageLineGroup::class),
            ],
        ];
    }

    /**
     * Inject route parameters into the Form Request for validation.
     *
     * @param null|array|mixed $keys
     *
     * @return array<string, string>
     */
    public function all($keys = null): array
    {
        // Add route parameters to validation data
        return array_merge(
            parent::all(),
            [
                'group' => $this->route('group'),
            ]
        );
    }
}
