<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate/permission as needed
    }

    public function rules(): array
    {
        return [
            'name'           => ['required','string','max:255'],
            'email'          => ['required','email','max:255','unique:users,email'],
            'password'       => ['required','string','min:6','max:255'],
            'avatar'         => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'permissions'    => ['nullable','array'],
            'permissions.*'  => ['integer', Rule::exists('permissions','id')],
        ];
    }
}
