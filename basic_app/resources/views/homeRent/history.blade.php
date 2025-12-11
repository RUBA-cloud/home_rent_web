@extends('adminlte::page')

@section('title', ' ' . __('adminlte::adminlte.home_rent_module') . ' ' . __('adminlte::adminlte.history'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Sidebar --}}
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">
            {{-- Action Buttons --}}
            <x-action_buttons
            label="{{__('adminlte::adminlte.employee')}}"
                addRoute="employees.create"
                historyRoute="employees.index"
                :showAdd="false"
            />

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                    ['key' => 'image', 'label' => __('adminlte::adminlte.permissions')],
                    ['key' => 'name', 'label' => __('adminlte::adminlte.full_name')],
                    ['key' => 'email', 'label' => __('adminlte::adminlte.email')],
                    ['key' => 'is_active', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp
  <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\HomeRentHistory"       {{-- any Eloquent model --}}
        detailsRoute="homeRent.show"   {{-- optional: blade partial for modal --}}
        editRoute="homeRent.edit"        {{-- route names (optional) --}}
        deleteRoute="homeRent.destroy"   {{-- when set, delete uses form+route --}}
        reactiveRoute="homeRent.reactivate"
        initial-route="{{ route('homeRent.history') }}" {{-- will reload to here if called --}}
        :search-in="['name','email']"
        :per-page="12"
    />


        </div>
    </main>
</div>
@endsection
