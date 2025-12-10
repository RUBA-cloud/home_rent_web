@extends('adminlte::page')
@section('title', __('adminlte::adminlte.offers_type'))

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}

           <x-action_buttons   label="{{__('adminlte::adminlte.offers_type')}}"
                addRoute="offers_type.create"
                historyRoute="offers_type.history"
                historyParams="true"
                :showAdd="true"
            />


        {{-- Additional Table Card --}}
        <div class="card">
            <div class="card-header">
   <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.offers_type')}}</h2>            </div>

            <div class="card-body table-responsive p-0">
                  @php
                $fields = [
                     ['key' => 'name_en', 'label' => __('adminlte::adminlte.name_en')],
                    ['key' => 'name_ar', 'label' => __('adminlte::adminlte.name_ar')],
                    ['key' => 'is_active', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp


<livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\OffersType"       {{-- any Eloquent model --}}
        detailsRoute="offers_type.show"   {{-- optional: blade partial for modal --}}
        editRoute="offers_type.edit"        {{-- route names (optional) --}}
        deleteRoute="offers_type.destroy"   {{-- when set, delete uses form+route --}}
        reactiveRoute="offers_type.reactivate"
        initial-route="{{ route('offers_type.index') }}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar']"
        :per-page="12"
    />
            </div>
        </div>
    </div>
@endsection
