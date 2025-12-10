{{-- resources/views/modules/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.modules'))

@section('content_header')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="flex-grow-1 mb-2 mb-md-0">
        <h1 class="m-0">{{ __('adminlte::adminlte.modules') }}</h1>
        <small class="text-muted d-block">
            {{ __('adminlte::adminlte.modules_manage_hint') }}
        </small>
    </div>

    {{-- Always visible "Create Module" button --}}
    <div class="text-md-right">
        <a href="{{ route('modules.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus mr-1"></i> {{ __('adminlte::adminlte.modules_create') }}
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- Toolbar --}}
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('modules.index') }}" method="GET" class="form-row">
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control"
                        placeholder="{{ __('adminlte::adminlte.search_by_user_or_module') }}">
                </div>

                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <select name="status" class="form-control">
                        <option value="">{{ __('adminlte::adminlte.all_status') }}</option>
                        <option value="active"   {{ request('status')=='active'   ? 'selected' : '' }}>{{ __('adminlte::adminlte.active') }}</option>
                        <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>{{ __('adminlte::adminlte.inactive') }}</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <select name="sort" class="form-control">
                        <option value="">{{ __('adminlte::adminlte.sort_recent') }}</option>
                        <option value="oldest"        {{ request('sort')=='oldest'        ? 'selected' : '' }}>{{ __('adminlte::adminlte.sort_oldest') }}</option>
                        <option value="enabled_desc"  {{ request('sort')=='enabled_desc'  ? 'selected' : '' }}>{{ __('adminlte::adminlte.sort_most_enabled') }}</option>
                        <option value="enabled_asc"   {{ request('sort')=='enabled_asc'   ? 'selected' : '' }}>{{ __('adminlte::adminlte.sort_least_enabled') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex">
                    <button class="btn btn-outline-secondary mr-2" type="submit">
                        <i class="fas fa-filter mr-1"></i> {{ __('adminlte::adminlte.filter') }}
                    </button>
                    <a href="{{ route('modules.index') }}" class="btn btn-outline-light border">
                        <i class="fas fa-undo mr-1"></i> {{ __('adminlte::adminlte.reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="far fa-check-circle mr-1"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Empty State --}}
    @if($modules->isEmpty())
        <div class="text-center py-5">
            <div class="h3 mb-2">{{ __('adminlte::adminlte.no_modules') }}</div>
            <p class="text-muted mb-3">{{ __('adminlte::adminlte.no_modules_hint') }}</p>
            <a href="{{ route('modules.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus mr-1"></i> {{ __('adminlte::adminlte.modules_create') }}
            </a>
        </div>
    @else

        {{-- Cards Grid --}}
        <div class="row">
            @foreach($modules as $module)
                @php
                    $featureFields = [
                        'company_dashboard_module'   => __('adminlte::adminlte.module_dashboard'),
                        'company_info_module'        => __('adminlte::adminlte.module_info'),
                        'company_branch_module'      => __('adminlte::adminlte.module_branch'),
                        'company_category_module'    => __('adminlte::adminlte.module_category'),
                        'company_type_module'        => __('adminlte::adminlte.module_type'),
                        'company_size_module'        => __('adminlte::adminlte.module_size'),
                        'company_offers_type_module' => __('adminlte::adminlte.module_offers_type'),
                        'company_offers_module'      => __('adminlte::adminlte.module_offers'),
                        'product_module'             => __('adminlte::adminlte.module_product'),
                        'employee_module'            => __('adminlte::adminlte.module_employee'),
                        'order_module'               => __('adminlte::adminlte.module_order'),
                        'order_status_module'        => __('adminlte::adminlte.order_status_module'),
                        'region_module'              => __('adminlte::adminlte.region_module'),
                        'company_delivery_module'    => __('adminlte::adminlte.company_delivery_module'),
                        'payment_module'             => __('adminlte::adminlte.payment_module'),
                        'additional_module'          => __('adminlte::adminlte.additional'),
                    ];

                    $enabledCount = collect(array_keys($featureFields))
                        ->filter(fn($f) => (bool) data_get($module, $f))
                        ->count();
                    $totalCount = count($featureFields);
                    $percent = $totalCount ? ($enabledCount / $totalCount) * 100 : 0;
                @endphp

                <div class="col-12 col-lg-6 col-xl-4 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-light d-flex align-items-center justify-content-between">
                            <div class="text-truncate" style="max-width: 70%;">
                                <strong>
                                    {{ optional($module->user)->name ?? __('adminlte::adminlte.global_unassigned') }}
                                </strong>
                                <div class="text-muted small">
                                    #{{ $module->id }} • {{ optional($module->created_at)->format('Y-m-d') }}
                                </div>
                            </div>
                            <span class="badge {{ $module->is_active ? 'badge-success' : 'badge-secondary' }}">
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $module->is_active ? '#28a745' : '#adb5bd' }};margin-right:6px;"></span>
                                {{ $module->is_active ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
                            </span>
                        </div>

                        <div class="card-body">
                            {{-- Enabled Summary --}}
                            <div class="mb-3">
                                <div class="small text-muted mb-1">{{ __('adminlte::adminlte.enabled_modules') }}</div>
                                <div class="progress" style="height: .9rem;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $percent }}%"
                                         aria-valuenow="{{ $enabledCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}">
                                        {{ $enabledCount }}/{{ $totalCount }}
                                    </div>
                                </div>
                            </div>

                            {{-- Pills --}}
                            <div class="d-flex flex-wrap">
                                @foreach ($featureFields as $field => $label)
                                    <span class="badge {{ $module->$field ? 'badge-success' : 'badge-light' }} mr-2 mb-2">
                                        <i class="fas {{ $module->$field ? 'fa-check-circle' : 'fa-minus-circle' }} mr-1"></i>
                                        {{ $label }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                {{ __('adminlte::adminlte.updated') }} {{ optional($module->updated_at)->diffForHumans() }}
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('modules.show', $module) }}" class="btn btn-default" title="{{ __('adminlte::adminlte.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('modules.edit', $module) }}" class="btn btn-default" title="{{ __('adminlte::adminlte.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('modules.destroy', $module) }}" method="POST"
                                      onsubmit="return confirm('{{ __('adminlte::adminlte.confirm_delete_module') }}')" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-default text-danger" title="{{ __('adminlte::adminlte.delete') }}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $modules->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
