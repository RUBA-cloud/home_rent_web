<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation (normalize booleans/ints coming as strings).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'excepted_day_count' => is_null($this->input('excepted_day_count'))
                ? null
                : (int) $this->input('excepted_day_count'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'country_en'         => ['required', 'string', 'max:120'],
            'country_ar'         => ['required', 'string', 'max:120'],
            'city_en'            => ['required', 'string', 'max:120'],
            'city_ar'            => ['required', 'string', 'max:120'],

            // If it's a number of days; adjust the max as you like
            'excepted_day_count' => ['nullable', 'integer', 'min:0', 'max:365'],

            // Checkbox/toggle; accepts true/false, 1/0, "on"/"off"
            'is_active'          => ['nullable', 'boolean'],

            // If this region belongs to a user; make nullable if optional
            'user_id'            => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Optional: friendly attribute names (useful for Arabic/English UIs).
     */
    public function attributes(): array
    {
        return [
            'country_en'         => 'country (EN)',
            'country_ar'         => 'country (AR)',
            'city_en'            => 'city (EN)',
            'city_ar'            => 'city (AR)',
            'excepted_day_count' => 'expected day count',
            'is_active'          => 'status',
            'user_id'            => 'user',
        ];
    }
}
