<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
</div>
@extends('adminlte::page')

@section('title', ' ' . __('adminlte::adminlte.orderStatus'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Sidebar --}}
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">
            {{-- Action Buttons --}}
            <x-action_buttons
            label="{{__('adminlte::adminlte.orderStatus')}}"
                addRoute="order_status.create"
                historyRoute="order_status.history"
                :showAdd="false"
            />

            {{-- Table Field Definitions --}}
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
        model="\App\Models\OrderStatusHistory"       {{-- any Eloquent model --}}
        detailsRoute="order_status.show"   {{-- optional: blade partial for modal --}}
        edit-route="order_status.edit"        {{-- route names (optional) --}}
        delete-route="order_status.destroy"   {{-- when set, delete uses form+route --}}
        reactive-route="order_status.reactivate"
        initial-route="{{ route('order_status.index') }}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar']"
        :per-page="12"
    />
        </div>

        </div>
    </main>
</div>
@endsection
