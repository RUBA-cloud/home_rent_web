<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomeRentRequest extends FormRequest
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
            // checkbox (hidden 0 + checkbox 1) → sometimes|boolean is enough
            'is_active' => ['sometimes', 'boolean'],

            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],

            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],

            // IMAGE (2MB)
            'image' => ['nullable', 'image', 'max:2048'],

            // PRICE (column is usually rent_price)
            'rent_price' => ['required', 'numeric', 'min:0'],

            // LOCATION
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],

            // FEATURES (IDs from home_features table via multi-select)
            'home_rent_features'   => ['nullable', 'array'],
            'home_rent_features.*' => ['integer', 'exists:home_features,id'],

            // ROOMS
            'number_of_bedrooms'  => ['required', 'integer', 'min:0'],
            'number_of_bathrooms' => ['required', 'integer', 'min:0'],

            // USER (we usually fill from Auth, so nullable is ok)
            'user_id' => ['nullable', 'exists:users,id'],

            // CATEGORY
            'category_id' => ['required', 'exists:categories,id'],

            // VIDEO (e.g. up to ~50MB)
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,wmv'],
        ];
    }
}
