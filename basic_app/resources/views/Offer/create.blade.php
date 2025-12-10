@extends('adminlte::page')

@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.offers'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.offers') }}</h1>
@stop

@section('content')
<div style="min-height: 100vh; display: flex; width: 100%;">
    <div style="width: 100%;">
@include('Offer.form', [
            'action'     => route('offers.store'),
            'method'     => 'POST',
            'offer' => null,
        ])
    </div>
</div>
@stop
