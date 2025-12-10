
 @extends('adminlte::page')

@section('title', ' ' . __('adminlte::adminlte.history'))

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-3" style="padding: 24px">
            <div class="col">
                <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.type')}}</h2>
            </div>
           <x-action_buttons
                addRoute="type.create"
                historyRoute="type.index"

                :showAdd="false"
            />
        </div>


        {{-- Sizes Table Card --}}
        <div class="card">
            <div class="card-header">
   <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.type')}}</h2>            </div>

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
        model="\App\Models\Type"       {{-- any Eloquent model --}}
        detailsRoute="type.show"   {{-- optional: blade partial for modal --}}
        editRoute="type.edit"        {{-- route names (optional) --}}
        deleteRoute="type.destroy"   {{-- when set, delete uses form+route --}}
        reactiveRoute="type.reactive"
        initial-route="{{ route('type.index')}}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar','description_en','description_ar']"
        :per-page="12"/>

            </div>
        </div>
    </div>
@endsection

