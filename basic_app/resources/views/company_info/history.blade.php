@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_info_history'))

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-3" style="padding: 24px">
            <div class="col">
                <h2 class="font-weight-bold text-dark">
                    {{ __('adminlte::adminlte.company_info_history') }}
                </h2>
            </div>

            <x-action_buttons
                label="{{ __('adminlte::adminlte.company_info_history') }}"
                addRoute="companyInfo.create"
                historyRoute="companyInfo.index"
                :showAdd="false"
            />
        </div>

        {{-- Company Info History Table Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title font-weight-bold text-dark mb-0">
                    {{ __('adminlte::adminlte.company_info_history') }}
                </h3>
            </div>

            <div class="card-body">
                @php
                    $fields = [
                        [
                            'key'   => 'image',
                            'label' => __('adminlte::adminlte.image'),
                            'type'  => 'image',
                        ],
                        [
                            'key'   => 'name_en',
                            'label' => __('adminlte::adminlte.company_name_en'),
                        ],
                        [
                            'key'   => 'name_ar',
                            'label' => __('adminlte::adminlte.company_name_ar'),
                        ],
                        [
                            'key'   => 'email',
                            'label' => __('adminlte::adminlte.company_email'),
                        ],
                        [
                            'key'   => 'phone',
                            'label' => __('adminlte::adminlte.company_phone'),
                        ],
                    ];
                @endphp

                <livewire:adminlte.data-table
                    :fields="$fields"  {{-- نفس fields اللي تبع الجدول --}}
                    model="\App\Models\CompanyInfoHistory" {{-- الموديل --}}
                    detailsRoute="companyInfo.show"
                    initial-route="{{ route('companyInfo.index') }}" {{-- يرجع لصفحة index --}}
                    :search-in="[
                        'name_en',
                        'name_ar',
                        'about_us_en',
                        'about_us_ar',
                        'vision_en',
                        'vision_ar',
                        'mission_en',
                        'mission_ar'
                    ]"
                    :per-page="12"
                />
            </div>
        </div>
    </div>
@endsection
