<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'home_id'       => ['required', 'integer', 'exists:home_rents,id'],
            'from_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after:date_from'],
            'adults_count'  => ['required', 'integer', 'min:1'],
            'children_count'  => ['required', 'integer', 'min:0'],
        ];
    }
}
