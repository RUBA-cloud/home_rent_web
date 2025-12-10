@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.size'))
@section('content')
    <div class="container-fluid">
        {{-- Page Header --}}
  <div class="card_table" style="padding: 24px">
           <x-action_buttons
           label="{{__('adminlte::adminlte.size')}}"
                addRoute="sizes.create"
                historyRoute="sizes.index"
                :showAdd="false"
            />
        </div>


        {{-- Sizes Table Card --}}
        <div class="card">
            <div class="card-header">
   <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.size')}}</h2>            </div>

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
        model="\App\Models\SizeHistory"       {{-- any Eloquent model --}}
        detailsRoute="sizes.show"   {{-- optional: blade partial for modal --}}
        editRoute="sizes.edit"        {{-- route names (optional) --}}
        deleteRoute="sizes.destroy"   {{-- when set, delete uses form+route --}}
        reactiveRoute="sizes.reactive"
        initial-route="{{ route('sizes.index')}}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar','description_en','description_ar']"
        :per-page="12"/>


            </div>
        </div>
    </div>
@endsection

