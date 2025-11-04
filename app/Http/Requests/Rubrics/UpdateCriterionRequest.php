<?php

namespace App\Http\Requests\Rubrics;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCriterionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => ['sometimes','string','max:255'],
            'weight'    => ['sometimes','numeric','min:0','max:1'],
            'max_score' => ['sometimes','integer','min:1'],
        ];
    }
}
