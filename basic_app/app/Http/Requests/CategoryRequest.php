<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Detect if this is an update (PUT/PATCH) vs create (POST)
        $isUpdate   = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $categoryId = $this->route('category'); // resource route param is usually 'category'

        // Base rules for both create & update
        $nameEnRules = ['required', 'string', 'max:25'];
        $nameArRules = ['required', 'string', 'max:25'];

        if (!$isUpdate) {
            // CREATE: enforce uniqueness
            $nameEnRules[] = Rule::unique('categories', 'name_en');
            $nameArRules[] = Rule::unique('categories', 'name_ar');
        }
        // UPDATE: allow duplicates (no unique rule)
        // If you prefer to keep uniqueness but ignore current row, use:
        // $nameEnRules[] = Rule::unique('categories','name_en')->ignore($categoryId);
        // $nameArRules[] = Rule::unique('categories','name_ar')->ignore($categoryId);

        return [
            'name_en' => $nameEnRules,
            'name_ar' => $nameArRules,

            'is_active' => ['nullable', 'boolean'],

            'branch_ids'   => ['nullable', 'array'],
            'branch_ids.*' => ['integer', 'exists:company_branches,id'],

            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        // falls back to your lang file if available
        return trans('adminlte::validation.attributes');
    }

    public function messages(): array
    {
        return trans('adminlte::validation.messages');
    }
}
