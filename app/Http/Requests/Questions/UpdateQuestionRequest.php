<?php

namespace App\Http\Requests\Questions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
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
            'stem' => ['sometimes','string'],
            'difficulty' => ['sometimes','nullable','string'],
            'topic' => ['sometimes','nullable','string'],
            'tags' => ['sometimes','nullable','array'],
            'points' => ['sometimes','integer'],
        ];
    }
}
