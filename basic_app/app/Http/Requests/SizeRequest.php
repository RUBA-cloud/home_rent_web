<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SizeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        // Detect ID from route for update (adjust 'size' if your route param is different)
        $sizeId = $this->route('size');

        return [
         'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'name_en' => [
                'required',
                'string',
                'max:25',
                Rule::unique('sizes', 'name_en')->ignore($sizeId)
            ],
            'name_ar' => [
                'required',
                'string',
                'max:25',
                Rule::unique('sizes', 'name_ar')->ignore($sizeId),
            ],
            'is_active' => ['boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'descripation'=>["nullable",'string','max:255']
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
