@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_branch'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">


    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
             {{-- Action Buttons --}}

                {{-- filepath: /Users/rubahammad/Desktop/basic_app3/basic_app/resources/views/CompanyBranch/index.blade.php --}}
<x-action_buttons
    label="{{__('adminlte::adminlte.company_branch')}}"
    addRoute="companyBranch.create"
    historyRoute="companyBranch.index"
    :showAdd="false"
/>
            {{-- Define Table Fields --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => __('adminlte::adminlte.branch_name_en')],
                    ['key' => 'name_ar', 'label' => __('adminlte::adminlte.branch_name_ar')],
                    ['key' => 'email', 'label' => __('adminlte::adminlte.email')],
                    ['key' => 'address_en', 'label' => __('adminlte::adminlte.company_address_en')],
                    ['key' => 'address_ar', 'label' => __('adminlte::adminlte.company_address_ar')],
                    ['key' => 'is_active', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],


                ];
            @endphp
             {{-- Table Component --}}
    <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\companyBranch"       {{-- any Eloquent model --}}
        details-view="companyBranch.show"   {{-- optional: blade partial for modal --}}
        edit-route="companyBranch.edit"        {{-- route names (optional) --}}
        delete-route="companyBranch.destroy"   {{-- when set, delete uses form+route --}}
        reactive-route="reactive_branch"
        initial-route="{{ route('companyBranch.index') }}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar','email','company_address_en','company_address_ar']"
        :per-page="12"
    />
        {{-- Pagination --}}
        <div style="margin-top: 24px;">
            {{ $branches->links() }}
        </div>
    </main>
</div>
@endsection
