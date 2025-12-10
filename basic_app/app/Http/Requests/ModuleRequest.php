<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // set to true if no authorization logic needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company_dashboard_module' => 'nullable|boolean',
            'company_info_module'      => 'nullable|boolean',
            'company_branch_module'    => 'nullable|boolean',
            'company_category_module'  => 'nullable|boolean',
            'company_type_module'      => 'nullable|boolean',
            'company_size_module'      => 'nullable|boolean',
            'company_offers_type_module' => 'nullable|boolean',
            'company_offers_module'    => 'nullable|boolean',
            'product_module'           => 'nullable|boolean',
            'employee_module'          => 'nullable|boolean',
            'order_module'             => 'nullable|boolean',
            'order_status_module'=>'nullable|boolean',
            'region_module'=>'nullable|boolean',
            'company_delivery_module'=>'nullable|boolean',
            'payment_module'=>'nullable|boolean',
            'is_active'=> 'nullable|boolean',
            'user_id' =>'nullable|exists:users,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // convert checkboxes (present → true, absent → false)
        $this->merge([
            'company_dashboard_module'  => $this->has('company_dashboard_module'),
            'company_info_module'       => $this->has('company_info_module'),
            'company_branch_module'     => $this->has('company_branch_module'),
            'company_category_module'   => $this->has('company_category_module'),
            'company_type_module'       => $this->has('company_type_module'),
            'company_size_module'       => $this->has('company_size_module'),
            'company_offers_type_module'=> $this->has('company_offers_type_module'),
            'company_offers_module'     => $this->has('company_offers_module'),
            'product_module'            => $this->has('product_module'),
            'employee_module'           => $this->has('employee_module'),
            'order_module'              => $this->has('order_module'),
            'is_active'                 => $this->has('is_active'),
        ]);
    }
}
