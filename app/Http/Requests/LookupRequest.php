<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_number' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
        ];
    }
}
