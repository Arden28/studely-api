<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'title' => ['required','string'],
            'code'  => ['nullable','string'],
            'start_at' => ['nullable','date'],
            'end_at'   => ['nullable','date','after_or_equal:start_at'],
            'per_student_time_limit_min' => ['nullable','integer','min:0'],
            'order' => ['nullable','integer','min:0'],
        ];
    }
}
