@extends('adminlte::page')
@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.employee'))


@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">{{ __('adminlte::adminlte.create') }}{{ __('adminlte::menu.employees') }}</h1>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>{{ __('adminlte::adminlte.go_back') }}</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @include('employee.form', [
            'action'     => route('employees.store'),
            'method'     => 'POST',
            'emp' => null,
        ])
    </div>
</div>
@endsection
