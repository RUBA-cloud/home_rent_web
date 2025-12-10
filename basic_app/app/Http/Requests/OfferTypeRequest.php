<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferTypeRequest extends FormRequest
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
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'is_discount' => 'nullable|boolean',
            'is_product_count_gift' => 'nullable|boolean',
            'is_total_gift' => 'nullable|boolean',
            'is_total_discount' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'discount_value_product' => 'nullable|numeric|min:0',
            'discount_value_delivery' => 'nullable|numeric|min:0',
            'products_count_to_get_gift_offer' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
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
