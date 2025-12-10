{{-- resources/views/offers_type/edit.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.update') . ' ' . ($offersType->name_en ?? __('adminlte::adminlte.offers_type')))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.update') }} {{ $offersType->name_en ?? '' }}</h1>
@stop

@section('content')
<div class="container-fluid py-3">
    @include('offers_type._form', [
        'action'     => route('offers_type.update', $offersType->id),
        'method'     => 'PUT',
        'offersType' => $offersType,
    ])
</div>
@stop
