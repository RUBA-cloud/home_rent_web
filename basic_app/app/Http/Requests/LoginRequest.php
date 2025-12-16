<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'     => ['required', 'email'],
            'password'  => ['required', 'string', 'min:8'],
            // ✅ double validation
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'latitude'  => ['required', 'numeric', 'between:-90,90'],

            'remember'  => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return trans('adminlte::validation.attributes');
    }

    public function messages(): array
    {
        return trans('adminlte::validation.messages');
    }
}
