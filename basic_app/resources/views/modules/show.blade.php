@extends('adminlte::page')

@section('title', __('adminlte::adminlte.module_details_title'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.module_n_hash', ['id' => $module->id]) }}</h1>
@stop

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <strong>{{ __('adminlte::adminlte.module_details_title') }}</strong>
                <span class="text-muted small">
                    {{ __('adminlte::adminlte.create', ['date' => optional($module->created_at)->format('Y-m-d')]) }}
                </span>
            </span>
            <span class="badge {{ $module->is_active ? 'badge-success' : 'badge-secondary' }}">
                {{ $module->is_active ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
            </span>
        </div>
        <div class="card-body">

            {{-- Assigned User --}}
            <p>
                <strong>{{ __('adminlte::adminlte.user') }}:</strong>
                @if($module->user)
                    {{ $module->user->name }}
                @else
                    <span class="text-muted">{{ __('adminlte::adminlte.unassigned') }}</span>
                @endif
            </p>

            {{-- Modules Status --}}
            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold">{{ __('adminlte::adminlte.group_company') }}</h5>
                    <ul class="list-group mb-3">
                        @foreach ([
                            'company_dashboard_module'   => __('adminlte::adminlte.module_dashboard'),
                            'company_info_module'        => __('adminlte::adminlte.module_info'),
                            'company_branch_module'      => __('adminlte::adminlte.module_branch'),
                            'company_category_module'    => __('adminlte::adminlte.module_category'),
                            'company_type_module'        => __('adminlte::adminlte.module_type'),
                            'company_size_module'        => __('adminlte::adminlte.module_size'),
                            'company_offers_type_module' => __('adminlte::adminlte.module_offers_type'),
                            'company_offers_module'      => __('adminlte::adminlte.module_offers'),
                        ] as $field => $label)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $label }}
                                <span class="badge {{ $module->$field ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $module->$field ? __('adminlte::adminlte.enabled') : __('adminlte::adminlte.disabled') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-md-6">
                    <h5 class="fw-bold">{{ __('adminlte::adminlte.group_other') }}</h5>
                    <ul class="list-group mb-3">
                        @foreach ([
                            'product_module'  => __('adminlte::adminlte.module_product'),
                            'employee_module' => __('adminlte::adminlte.module_employee'),
                            'order_module'    => __('adminlte::adminlte.module_order'),
                        ] as $field => $label)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $label }}
                                <span class="badge {{ $module->$field ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $module->$field ? __('adminlte::adminlte.enabled') : __('adminlte::adminlte.disabled') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Meta --}}
            <p class="text-muted small">
                {{ __('adminlte::adminlte.last_updated_human', ['when' => optional($module->updated_at)->diffForHumans()]) }}
            </p>
 {{-- Actions --}}
                 <div class="d-flex justify-content-end mt-4">

                    <div class="col-12 pt-3">
                        <a href="{{ route('modules.edit', $module->id) }}"
                           class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{route('modules.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>
                 </div>
    </div>
    </div>
</div>
@endsection
