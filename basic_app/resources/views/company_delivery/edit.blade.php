@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.company_delivery'))

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 24px;">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin:0;">
                {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.company_delivery_module') }}
            </h2>

            <a href="{{ route('company_delivery.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('adminlte::adminlte.back') }}
            </a>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
 @include('company_delivery.form', [
            'action'     => route('company_delivery.update',$companyDelivery->id),
            'method'     => 'put',
            'delivery' => $companyDelivery,
        ])

    </div>
</div>
@endsection
