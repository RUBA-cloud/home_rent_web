@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_branch'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">

            <x-action_buttons
                label="{{ __('adminlte::adminlte.company_branch') }}"
                addRoute="companyBranch.create"

                :showAdd="true"
            />
        </div>

        {{-- Define Table Fields --}}
        @php
            $fields = [
                [
                    'key'   => 'name_en',
                    'label' => __('adminlte::adminlte.branch_name_en'),
                ],
                [
                    'key'   => 'name_ar',
                    'label' => __('adminlte::adminlte.branch_name_ar'),
                ],
                [
                    'key'   => 'is_active',
                    'label' => __('adminlte::adminlte.active'),
                    'type'  => 'bool',
                ],
                [
                    'key'   => 'user.name',
                    'label' => __('adminlte::adminlte.user_name'),
                ],
                [
                    'key'   => 'user.id',
                    'label' => __('adminlte::adminlte.user_id'),
                ],
            ];
        @endphp

        {{-- Table Component --}}
        <livewire:adminlte.data-table
            :fields="$fields"                                   {{-- columns --}}
            model="\App\Models\CompanyBranch"                   {{-- Eloquent model --}}
            details-route="companyBranch.show"                  {{-- route names --}}
            edit-route="companyBranch.edit"

            delete-route="companyBranch.destroy"
            reactive-route="companyBranch.reactivate"
            initial-route="{{ route('companyBranch.index') }}"
            :search-in="['name_en','name_ar','email','company_address_en','company_address_ar']"
            :pagination-in-table="true"                         {{-- 🔹 pagination in table --}}
        />

    </main>
</div>
@endsection
