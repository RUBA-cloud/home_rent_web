<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate/permission as needed
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')->id;

        return [
            'name'           => ['required','string','max:255'],
            'email'          => ['required','email','max:255', Rule::unique('users','email')->ignore($employeeId)],
            'password'       => ['nullable','string','min:6','max:255'],
            'avatar'         => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'permissions'    => ['nullable','array'],
            'permissions.*'  => ['integer', Rule::exists('permissions','id')],
           'is_active'          => ['nullable|boolean'],

        ];
    }
}
