<?php

namespace App\Http\Requests\Evaluation;

use Illuminate\Foundation\Http\FormRequest;

class ScoreAttemptRequest extends FormRequest
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
            'scores' => ['required','array','min:1'],
            'scores.*.criterion_id' => ['required','integer','exists:rubric_criteria,id'],
            'scores.*.score'        => ['required','integer','min:0'],
            'scores.*.comment'      => ['nullable','string','max:2000'],
        ];
    }
}
