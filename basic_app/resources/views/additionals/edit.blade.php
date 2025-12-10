@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.additional'))
@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-additional: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
        {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.additional') }}

        </h2>

        @include('additionals.form', [
            'action'     => route('additional.update', $additional->id),
            'method'     => 'PUT',
            'additional' => $additional,
        ])
@endsection
