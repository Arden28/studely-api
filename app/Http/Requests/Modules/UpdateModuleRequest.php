<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
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
            'title' => ['sometimes','string'],
            'code'  => ['sometimes','nullable','string'],
            'start_at' => ['sometimes','nullable','date'],
            'end_at'   => ['sometimes','nullable','date','after_or_equal:start_at'],
            'per_student_time_limit_min' => ['sometimes','integer','min:0'],
            'order' => ['sometimes','integer','min:0'],
        ];
    }
}
