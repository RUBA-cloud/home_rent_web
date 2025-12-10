<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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

            /*
             |-----------------------------------------
             | اختيارات مفردة (من الموبايل مثلاً)
             |-----------------------------------------
             */
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'type_id'     => ['sometimes', 'nullable', 'integer', 'exists:type,id'],
            'size_id'     => ['sometimes', 'nullable', 'integer', 'exists:sizes,id'],
            'color'       => ['sometimes', 'nullable', 'string', 'max:100'],

            /*
             |-----------------------------------------
             | مصفوفات (لو حابة تدعم فلترة متعددة)
             |-----------------------------------------

            'search' => ['sometimes', 'nullable', 'string', 'max:150'],

            /*
             |-----------------------------------------
             | نطاق السعر (اختياري)
             |-----------------------------------------
             */
            'price_from' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_to'   => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
