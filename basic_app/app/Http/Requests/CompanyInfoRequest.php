<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyInfoRequest extends FormRequest
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'address_en' => 'required|string|max:255',
            'address_ar' => 'required|string|max:255',
            'location' => 'nullable|url|max:255', // Assuming this is a
            'about_us_en' => 'nullable|string|max:1000',
            'about_us_ar' => 'nullable|string|max:1000',
            'mission_en' => 'nullable|string|max:1000',
            'mission_ar' => 'nullable|string|max:1000',
            'vision_en' => 'nullable|string|max:1000',
            'vision_ar' => 'nullable|string|max:1000',
            'main_color' => 'nullable|string|max:7', // Assuming this is a hex color code
            'sub_color' => 'nullable|string|max:7', // Assuming this is a hex color code
            'text_color' => 'nullable|string|max:255',
            'button_color' => 'nullable|string|max:255', // Assuming this is a hex color code
            'icon_color' => 'nullable|string|max:255', // Assuming this is a hex color code
            'text_filed_color' => 'nullable|string|max:255',
            'hint_color' => 'nullable|string|max:255', // Assuming this is a hex color code
            'button_text_color' => 'nullable|string|max:255', // Assuming this is a hex color code
            'card_color' => 'nullable|string|max:255', // Assuming this is a hex color code
            'label_color' => 'nullable|string|max:255', //
            'phone' =>  'nullable|regex:/^07\d{8}$/',
            'twitter'=>'nullable|url|max:255',
            'instagram'=>'nullable||url|max:255',
            'facebook'=>'nullable|url|max:255',
             // Assuming
            'is_active' => 'boolean', // Assuming this is a boolean field for branch activity status
            // Assuming this is
            //
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
