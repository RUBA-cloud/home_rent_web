@extends('adminlte::page')

@section('title', __('adminlte::adminlte.regions'))

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <x-action_buttons
            label="{{ __('adminlte::adminlte.regions') }}"
            addRoute="regions.create"
            historyRoute="regions.index"
            :historyParams="false"
            :showAdd="false"
        />

        {{-- Table Card --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-weight-bold text-dark">{{ __('adminlte::adminlte.regions') }}</h2>
            </div>

            <div class="card-body table-responsive p-0">
                @php
                    $fields = [
                        ['key' => 'country_en', 'label' => __('adminlte::adminlte.country') . ' EN'],
                        ['key' => 'country_ar', 'label' => __('adminlte::adminlte.country') . ' AR'],
                        ['key' => 'city_en',    'label' => __('adminlte::adminlte.city')    . ' EN'],
                        ['key' => 'city_ar',    'label' => __('adminlte::adminlte.city')    . ' AR'],
                        ['key' => 'is_active',  'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                        ['key' => 'user.name',  'label' => __('adminlte::adminlte.user_name')],
                        ['key' => 'user.id',    'label' => __('adminlte::adminlte.user_id')],
                    ];
                @endphp

                <livewire:adminlte.data-table
                    :fields="$fields"
                    :model="App\Models\RegionHistory::class"
                    detailsRoute="regions.show"
                    editRoute="regions.edit"
                    deleteRoute="regions.destroy"
                    reactiveRoute="regions.restore"
                    :initialRoute="url()->current()"
                    :search-in="['country_en','country_ar','city_en','city_ar']"
                    :per-page="12"
                />
            </div>
        </div>
    </div>
@endsection
