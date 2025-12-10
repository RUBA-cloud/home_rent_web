@extends('adminlte::page')

@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.product'))

@php($isAr = app()->getLocale()==='ar')

@section('content')
<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">{{ __('adminlte::adminlte.create') }} {{ __('adminlte::adminlte.product') }}</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('Product.form', [
                'action' => route('product.store'),
                'method' => 'POST',
                'product' => null,
            ])
        </div>
    </div>
</div>
@endsection

