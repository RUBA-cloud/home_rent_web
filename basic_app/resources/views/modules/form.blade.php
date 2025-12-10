{{-- resources/views/modules/_form.blade.php --}}

@php
    /**
     * Shared Modules form.
     *
     * Expects:
     *  - $action (string)  → form action URL/route
     *  - $method (string)  → 'POST' | 'PUT' | 'PATCH'
     *  - $module (Model|null) optional → existing record for edit
     *  - $submitLabel (string|null) optional → button text
     */

    $module     = $module ?? null;
    $httpMethod = strtoupper($method ?? 'POST');
    $submitLabel = $submitLabel ?? __('adminlte::adminlte.save_information');

    // Company group fields
    $companyFields = [
        'additional_module'        => __('adminlte::adminlte.additional'),
        'company_dashboard_module' => __('adminlte::adminlte.company_dashboard_module'),
        'company_info_module'      => __('adminlte::adminlte.company_info_module'),
        'company_branch_module'    => __('adminlte::adminlte.company_branch_module'),
        'company_category_module'  => __('adminlte::adminlte.company_category_module'),
        'company_type_module'      => __('adminlte::adminlte.company_type_module'),
        'company_size_module'      => __('adminlte::adminlte.company_size_module'),
        'company_offers_type_module' => __('adminlte::adminlte.company_offers_type_module'),
        'company_offers_module'    => __('adminlte::adminlte.company_offers_module'),
        'order_status_module'      => __('adminlte::adminlte.order_status_module'),
        'region_module'            => __('adminlte::adminlte.region_module'),
        'payment_module'           => __('adminlte::adminlte.payment_module'),
        'company_delivery_module'  => __('adminlte::adminlte.company_delivery_module'),
    ];

    // Other group fields
    $otherFields = [
        'product_module'  => __('adminlte::adminlte.product_module'),
        'employee_module' => __('adminlte::adminlte.employee_module'),
        'order_module'    => __('adminlte::adminlte.order_module'),
        'is_active'       => __('adminlte::adminlte.is_active'),
    ];

    /**
     * Get checkbox value: prefer old(), then model, then default.
     */
    $checkedValue = function (string $field, int $default = 0) use ($module) {
        $modelVal = $module ? (int) ($module->{$field} ?? 0) : $default;
        return (int) old($field, $modelVal);
    };
@endphp

{{-- Validation errors (shared) --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST">
    @csrf
    @unless(in_array($httpMethod, ['GET', 'POST']))
        @method($httpMethod)
    @endunless

    <div class="row">
        {{-- Company Modules Card --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white font-weight-bold">
                    {{ __('adminlte::adminlte.group_company') }}
                </div>
                <div class="card-body">
                    @foreach ($companyFields as $field => $label)
                        <div class="form-check mb-2">
                            {{-- ensure unchecked posts 0 --}}
                            <input type="hidden" name="{{ $field }}" value="0">
                            <input type="checkbox"
                                   name="{{ $field }}"
                                   value="1"
                                   class="form-check-input"
                                   id="{{ $field }}"
                                   {{ $checkedValue($field, 0) ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Other Modules Card --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white font-weight-bold">
                    {{ __('adminlte::adminlte.group_other') }}
                </div>
                <div class="card-body">
                    @foreach ($otherFields as $field => $label)
                        <div class="form-check mb-2">
                            <input type="hidden" name="{{ $field }}" value="0">
                            <input type="checkbox"
                                   name="{{ $field }}"
                                   value="1"
                                   class="form-check-input"
                                   id="{{ $field }}"
                                   {{-- default "is_active" to true on create --}}
                                   {{ $field === 'is_active'
                                        ? ($checkedValue($field, 1) ? 'checked' : '')
                                        : ($checkedValue($field, 0) ? 'checked' : '') }}>
                            <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Action Button --}}
    <div class="form-group">
        <x-adminlte-button
            label="{{ $submitLabel }}"
            type="submit"
            theme="success"
            class="w-100"
            icon="fas fa-save"
        />
    </div>
</form>
