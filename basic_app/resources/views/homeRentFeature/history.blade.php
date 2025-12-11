@extends('adminlte::page')

@section('title', ' ' . __('adminlte::adminlte.home_rent_feature') . ' ' . __('adminlte::adminlte.history'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Sidebar --}}
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">
            {{-- Action Buttons --}}
            <x-action_buttons
            label="{{__('adminlte::adminlte.home_rent_feature')}}"
                addRoute="homeRentFeatures.create"
                historyRoute="homeRentFeatures.index"
                :showAdd="false"
            />

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                    ['key' => 'name', 'label' => __('adminlte::adminlte.name_en')],
                    ['key' => 'name_ar', 'label' => __('adminlte::adminlte.name_ar')],
                    ['key' => 'is_active', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp
  <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\HomeFeatureHistory"       {{-- any Eloquent model --}}
        detailsRoute="homeRentFeatures.show"   {{-- optional: blade partial for modal --}}
        editRoute="homeRentFeatures.edit"        {{-- route names (optional) --}}
        deleteRoute="homeRentFeatures.destroy"   {{-- when set, delete uses form+route --}}
        reactiveRoute="homeRentFeatures.reactivate"
        initial-route="{{ route('homeRentFeatures.history') }}" {{-- will reload to here if called --}}
        :search-in="['name','email']"
        :per-page="12"
    />


        </div>
    </main>
</div>
@endsection
