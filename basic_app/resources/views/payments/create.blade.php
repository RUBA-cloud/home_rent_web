@extends('adminlte::page')

@section('title', __('adminlte::adminlte.payment'))

@section('content')
    @include('payments.form', [
        'payment' => null,
        'action'  => 'payment.store',
        'method'  => 'POST',
    ])
@endsection
