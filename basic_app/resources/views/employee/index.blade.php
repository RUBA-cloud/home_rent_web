@extends('adminlte::page')

@section('title', ' ' . __('adminlte::adminlte.employee'))

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
                historyRoute="employees.history"
                :showAdd="true"
            />

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                    ['key' => 'name', 'label' => __('adminlte::adminlte.full_name')],
                    ['key' => 'email', 'label' => __('adminlte::adminlte.email')],

                    ['key' => 'id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp
  <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\User"       {{-- any Eloquent model --}}
        detailsRoute="employees.show"  {{-- optional: blade partial for modal --}}
        edit-route="employees.edit"        {{-- route names (optional) --}}
        delete-route="employees.destroy"   {{-- when set, delete uses form+route --}}
        reactive-route="employees.reactivate"

        initial-route="{{ route('employees.index') }}" {{-- will reload to here if called --}}
        :search-in="['name','email']"
        :per-page="12"
    />
        </div>
        </div>
    </main>
</div>
@endsection
