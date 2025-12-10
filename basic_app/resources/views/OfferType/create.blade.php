{{-- resources/views/offers_type/create.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.offers_type', [], app()->getLocale()) )

@section('content_header')
    <h1>{{ __('adminlte::adminlte.create') }} {{ __('adminlte::adminlte.offers_type') }}</h1>
@stop

@section('content')
<div class="container-fluid py-3">
    @include('offerType.form', [
        'action'     => route('offers_type.store'),
        'method'     => 'POST',
        'offersType' => null,
    ])
</div>
@stop
