@extends('adminlte::page')

@section('title', __('adminlte::adminlte.payment'))

@section('content')
    @include('payments.form', [
        'payment' => $payment,
        'action'  => 'payment.update',
        'method'  => 'PUT',
    ])
@endsection
