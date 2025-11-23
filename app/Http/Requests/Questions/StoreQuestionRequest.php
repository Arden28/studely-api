<?php

namespace App\Http\Requests\Questions;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
            'assessment_id' => ['nullable','integer','exists:assessments,id'],
            'type' => ['required','in:MCQ,OPEN'],
            'stem' => ['required','string'],
            'difficulty' => ['nullable','string'],
            'topic' => ['nullable','string'],
            'tags' => ['nullable','array'],
            'points' => ['nullable','integer'],
            'options' => ['nullable','array'], // for MCQ
            'options.*.label' => ['required_with:options','string'],
            'options.*.is_correct' => ['boolean'],
        ];
    }
}
