<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get the current branch id if updating
        $branchId = $this->route('companyBranch');
        // Change 'company_branch' to your actual route param if it's different (e.g., 'id')

        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_id' => 'nullable|exists:company_info,id',

            'name_en' => [
                'required',
                'string',
                'max:25',
                Rule::unique('company_branches', 'name_en')
                    ->ignore($branchId)
            ],

            'name_ar' => [
                'required',
                'string',
                'max:25',
                Rule::unique('company_branches', 'name_ar')
                    ->ignore($branchId)
            ],

            'phone' => [
                'nullable',
                'regex:/^07\d{8}$/',
                Rule::unique('company_branches', 'phone')
                    ->ignore($branchId)
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('company_branches', 'email')
                    ->ignore($branchId)
            ],

            'is_main_branch' => 'boolean',
            'is_active' => 'required|boolean',
            'address_en' => 'required|string|max:255',
            'address_ar' => 'required|string|max:255',
            'location' => 'nullable|url|max:255',
            'working_hours' => 'nullable|string|max:255',
'working_days'       => 'nullable|array',
            'working_days.*'     => 'string|max:20',
            'fax' => 'nullable|string|max:15',
            'working_hours_from' => 'nullable|string|max:255',
            'working_hours_to' => 'nullable|string|max:255',
        ];
    }
}
