@extends('adminlte::page')
@section('title', ' ' . __('adminlte::adminlte.permissions'))
@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Sidebar --}}
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">{{__('adminlte::adminlte.permissions')}}</h2>
            {{-- Action Buttons --}}
            <x-action_buttons
                addRoute="permissions.create"
                historyRoute=""
                :showAdd="true"
            />

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                     ['key' => 'name_en', 'label' => __('adminlte::adminlte.name_en')],
                    ['key' => 'name_ar', 'label' => __('adminlte::adminlte.name_ar')],
                     ['key' => 'can_add', 'label' => __('adminlte::adminlte.add'), 'type' => 'bool'],

                    ['key' => 'can_edit', 'label' => __('adminlte::adminlte.edit'), 'type' => 'bool'],
                    ['key' => 'can_view_history', 'label' => __('adminlte::adminlte.view_history'), 'type' => 'bool'],
                    ['key' => 'is_active', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'can_delete', 'label' => __('adminlte::adminlte.edit'), 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp

<livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\Permission"       {{-- any Eloquent model --}}
        detailsRoute="permissions.show"   {{-- optional: blade partial for modal --}}
        editRoute="permissions.edit"        {{-- route names (optional) --}}
        deleteRoute="permissions.destroy"   {{-- when set, delete uses form+route --}}
        reactiveRoute="permissions.reactivate"
        initial-route="{{ route('permissions.index')}}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar']"
        :per-page="12"/>


        </div>
    </main>
</div>
@endsection
