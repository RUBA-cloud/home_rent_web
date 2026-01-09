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

    protected function isUpdate(): bool
    {
        return in_array($this->method(), ['PUT', 'PATCH'], true);
    }

    public function rules(): array
    {
        $isUpdate = $this->isUpdate();

        return [
            'name_en'     => ['required', 'string', 'max:255'],
            'name_ar'     => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'address_en'  => ['required', 'string', 'max:255'],
            'address_ar'  => ['required', 'string', 'max:255'],

            'longitude'   => ['required', 'numeric', 'between:-180,180'],
            'latitude'    => ['required', 'numeric', 'between:-90,90'],

            'number_of_bedrooms'  => ['required', 'integer', 'min:0'],
            'number_of_bathrooms' => ['required', 'integer', 'min:0'],

            'rent_price'  => ['required', 'numeric', 'min:0'],

            'description_en' => ['required', 'string'],
            'description_ar' => ['required', 'string'],

            'size' => ['required', 'string', 'max:255'],

            'is_available' => ['sometimes', 'boolean'],
            'is_feature' => ['sometimes', 'boolean'],

            // ✅ Create: required | Update: nullable (keep old if not uploaded)
            'image' => [
                $isUpdate ? 'nullable' : 'required',
                'image',
                'max:2048',
            ],


            'video' => [
                $isUpdate ? 'nullable' : 'required',
                'file',
                'max:10240',
                'mimetypes:video/mp4,video/webm,video/ogg',
            ],

            // ✅ IMPORTANT:
            // - Create: sometimes|array (or required if you want)
            // - Update: sometimes|array (if field missing => keep old)
            'home_rent_features'   => ['sometimes', 'array'],
            'home_rent_features.*' => ['integer', 'exists:home_features,id'],

            'payment_way' => ['required', 'string', Rule::in(['daily', 'monthly'])],
            'payment_status' => ['required', 'integer', Rule::in([0, 1, 2])],
        ];
    }

    public function messages(): array
    {
        return [
            'home_rent_features.*.exists' => 'One or more selected features are invalid.',
            'payment_way.in'             => 'Payment period must be daily or monthly.',
            'payment_status.in'          => 'Payment status must be unpaid, paid, or pending.',
            'image.required'             => 'Image is required when creating.',
            'video.required'             => 'Video is required when creating.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_available'   => $this->has('is_available') ? (int) $this->boolean('is_available') : 0,
            'payment_status' => $this->filled('payment_status') ? (int) $this->input('payment_status') : null,
        ]);

        // ✅ DO NOT set home_rent_features if it's missing on update
        // (so controller can "keep old")
        // If it exists but empty, that's intentional (user cleared it).
    }
}
