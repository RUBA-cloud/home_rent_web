@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.type'))
@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">
        <h2 style="font-type: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
                    {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.type') }}
        </h2>
        @include('type.form', [
            'action'     => route('type.update', $type->id),
            'method'     => 'PUT',
            'type' => $type,
        ])
    </div>
</div>
@endsection
