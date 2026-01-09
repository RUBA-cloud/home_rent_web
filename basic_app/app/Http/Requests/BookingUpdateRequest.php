<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ✅ fix table name + column + allow uuid? (kept integer as you wrote)
            'booking_id'      => ['required', 'integer', 'exists:booking_table,id'],

            // ✅ validate dates properly
            'from_date'       => ['nullable', 'date'],
            'end_date'        => ['nullable', 'date', 'after:from_date'],

            // ✅ counts
            'adults_count'    => ['nullable', 'integer', 'min:1'],
            'childern_count'  => ['nullable', 'integer', 'min:0'], // children can be 0 عادة
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after' => 'The end_date must be a date after from_date.',
        ];
    }
}
