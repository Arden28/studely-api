<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterInitRequest extends FormRequest
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
            'full_name'        => ['required','string','max:255'],
            'institution_name' => ['nullable','string','max:255'],
            'university_id'    => ['required','integer','exists:colleges,id'],
            'mobile'           => ['required','string','max:30'], // E.164 preferred
            'email'            => ['required','email','max:255'],
            'gender'           => ['required','string','max:20'],
            'dob'              => ['required','date','before:today'],
            'admission_year'   => ['required','integer','min:1900','max:'.(int)date('Y')],
            'current_semester' => ['required','integer','min:1','max:12'],
            'reg_no'           => ['required','string','max:100'],

            // step 2 (password rules: min 8, alphanumeric, at least 1 special)
            'password'         => [
                'required','string','min:8',
                'regex:/^(?=.*[0-9])(?=.*[A-Za-z])(?=.*[^A-Za-z0-9]).{8,}$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password must be at least 8 chars, alphanumeric, and contain at least 1 special character.',
        ];
    }
}
