<?php

namespace App\Http\Requests\Api\Resources;

use App\Enums\LanguageLineGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetLanguageLinesRequest extends FormRequest
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
     * @return array<mixed>
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
