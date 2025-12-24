<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomeRentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en'     => ['required', 'string', 'max:255'],
            'name_ar'     => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'address_en'  => ['required', 'string', 'max:255'],
            'address_ar'  => ['required', 'string', 'max:255'],

            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],

            'number_of_bedrooms'  => ['nullable', 'integer', 'min:0'],
            'number_of_bathrooms' => ['nullable', 'integer', 'min:0'],

            'rent_price'  => ['nullable', 'numeric', 'min:0'],
            'price'       => ['nullable', 'numeric', 'min:0'],

            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],

            // checkbox (optional)
            'is_available' => ['sometimes', 'boolean'],
            'size'=>['required'],

            // files
            'image' => ['nullable', 'image', 'max:2048'], // 2MB
            'video' => [
                'nullable',
                'file',
                'max:10240', // 10MB (KB)
                'mimetypes:video/mp4,video/webm,video/ogg',
            ],

            // Many-to-many feature IDs
            'home_rent_features'   => ['sometimes', 'array'],
            'home_rent_features.*' => ['integer', 'exists:home_features,id'],

            // payment period: daily | monthly
            'payment_way' => ['nullable', 'string', Rule::in(['daily', 'monthly'])],

            // payment status: 0 unpaid | 1 paid | 2 pending
            'payment_status' => ['nullable', 'integer', Rule::in([0, 1, 2])],
        ];
    }

    public function messages(): array
    {
        return [
            'home_rent_features.*.exists' => 'One or more selected features are invalid.',
            'payment_way.in'             => 'Payment period must be daily or monthly.',
            'payment_status.in'          => 'Payment status must be unpaid, paid, or pending.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_available'   => $this->has('is_available') ? $this->boolean('is_available') : null,
            'payment_status' => $this->filled('payment_status') ? (int) $this->input('payment_status') : null,
        ]);
    }
}
