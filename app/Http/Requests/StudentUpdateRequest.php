<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'national_id_number' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'acknowledge' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'acknowledge.accepted' => 'Please tick the box to confirm you understand this can only be submitted once.',
        ];
    }
}
