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
            label="{{__('adminlte::adminlte.home_rent_module')}}"
                addRoute="homeRent.create"
                historyRoute="homeRent.index"
                :showAdd="true"
            />

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => __('adminlte::adminlte.name_en')],
                    ['key' => 'name_ar', 'label' => __('adminlte::adminlte.name_ar')],
                    ['key' => 'is_available', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp
  <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\HomeRent"       {{-- any Eloquent model --}}
        detailsRoute="homeRent.show"  {{-- optional: blade partial for modal --}}
        edit-route="homeRent.edit"        {{-- route names (optional) --}}
        delete-route="homeRent.destroy"   {{-- when set, delete uses form+route --}}
        reactive-route="homeRent.reactivate"
        initial-route="{{ route('homeRent.index') }}" {{-- will reload to here if called --}}
        :search-in="['name','email']"
        :per-page="12"
    />
        </div>
        </div>
    </main>
</div>
@endsection
