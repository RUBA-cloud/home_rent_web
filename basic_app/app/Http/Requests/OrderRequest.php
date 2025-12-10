<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        'products'                      => ['required', 'array', 'min:1'],
        'products.*.product_id'         => ['required', 'integer', 'exists:products,id'],
        'products.*.size_id'            => ['required', 'integer', 'exists:sizes,id'],
        'products.*.quantity'           => ['required', 'integer', 'min:1'],
        'products.*.colors'             => ['required', 'array', 'min:1'],
        'products.*.colors.*'           => ['string', 'max:50'],

        'address'                       => ['required', 'string', 'max:255'],
        'street_name'                   => ['required', 'string', 'max:255'],
        'building_number'               => ['required', 'string', 'max:50'],
        'lat'                           => ['required', 'numeric', 'between:-90,90'],
        'long'                          => ['required', 'numeric', 'between:-180,180'],
        'total_price'                   => ['nullable', 'numeric'],
    ];
}

}
