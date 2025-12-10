<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust if needed for auth logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_en'=>'required',
            'name_ar'=>'required',
            'description_en'=>'required',
            'description_ar'=>'required',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'type_id' => 'integer|exists:type,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ];
    }
    protected function prepareForValidation()
{
    $this->merge([
        'start_date' => $this->convertDate($this->start_date),
        'end_date' => $this->convertDate($this->end_date),
    ]);
}

private function convertDate($date)
{
    // Expecting format: DD/MM/YYYY
    $parsed = \DateTime::createFromFormat('d/m/Y', $date);
    return $parsed ? $parsed->format('Y-m-d') : $date; // Fall back to original if parsing fails
}

}
