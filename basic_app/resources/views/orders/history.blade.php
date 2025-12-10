@extends('adminlte::page')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">{{ __('adminlte::menu.orders') }}</h3>

    <x-action_buttons
        label="{{ __('adminlte::adminlte.orders') }}"
        addRoute="orders.create"
        historyRoute="orders.history"
        :showAdd="false"
    />

    <form class="d-flex" method="GET">
      <select class="form-control"  name="status" onchange="this.form.submit()">
        <option value="">{{ __('adminlte::adminlte.all') ?: 'All' }}</option>
        <option value="0" @selected(request('status')==='0')>{{ __('adminlte::adminlte.pending') ?: 'Pending' }}</option>
        <option value="1" @selected(request('status')==='1')>{{ __('adminlte::adminlte.accepted') ?: 'Accepted' }}</option>
        <option value="2" @selected(request('status')==='2')>{{ __('adminlte::adminlte.rejected') ?: 'Rejected' }}</option>
        <option value="3" @selected(request('status')==='3')>{{ __('adminlte::adminlte.completed') ?: 'Completed' }}</option>
      </select>
    </form>
  </div>

  @php
      $fields = [
          ['key' => 'status',        'label' => __('adminlte::adminlte.status') ?: 'Status', 'type' => 'status'],
          ['key' => 'user.name',     'label' => __('adminlte::adminlte.user_name') ?: 'User Name'],
          ['key' => 'offer.name_en', 'label' => (__('adminlte::adminlte.offer_name') ?: 'Offer Name').' (EN)'],
          ['key' => 'offer.name_ar', 'label' => (__('adminlte::adminlte.offer_name') ?: 'Offer Name').' (AR)'],
          ['key' => 'user.id',       'label' => __('adminlte::adminlte.user_id') ?: 'User ID'],
      ];
  @endphp

  {{-- Table Component --}}
  <livewire:adminlte.data-table
      :fields="$fields"
      :model="\App\Models\Order::class"
      details-route="orders.show"
      edit-route="orders.edit"
      delete-route="orders.destroy"
      reactive-route="orders.reactivate"
      initial-route="{{ request()->fullUrl() }}"
      :search-in="['id']"
      :per-page="12"
      {{-- Optional: pass current status filter to your component if it supports it --}}
      :filters="['status' => request('status')]"
  />
</div>
@endsection
