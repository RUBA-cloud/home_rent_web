@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.offer'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.offer') }}</h1>
@stop

@section('content')
    <div style="min-height: 100vh; display: flex; width: 100%;">
        <div style="width: 100%;">
            @include('Offer.form', [
                'action' => route('offers.update', $offer->id), // تمرير id للروت
                'method' => 'PUT',                              // خليها كبيرة عشان الpartial يتصرف
                'offer'  => $offer,
            ])
        </div>
    </div>
@stop
