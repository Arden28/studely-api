<?php

namespace App\Http\Requests\Rubrics;

use Illuminate\Foundation\Http\FormRequest;

class StoreRubricRequest extends FormRequest
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
            'assessment_id' => ['required','integer','exists:assessments,id'],
            'title'         => ['required','string','max:255'],
            'criteria'      => ['nullable','array'],
            'criteria.*.name'      => ['required_with:criteria','string','max:255'],
            'criteria.*.weight'    => ['required_with:criteria','numeric','min:0','max:1'],
            'criteria.*.max_score' => ['required_with:criteria','integer','min:1'],
        ];
    }
}
