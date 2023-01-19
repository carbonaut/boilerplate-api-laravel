<?php

namespace App\Http\Requests\Api\Maintenance;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class MaintenanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('toggleMaintenance');
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
