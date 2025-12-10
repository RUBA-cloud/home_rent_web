{{-- resources/views/modules/create.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.modules_title'))

@section('content')
<div class="container">
    <div class="card p-3">
        <h2 class="mb-4">{{ __('adminlte::adminlte.create') }}</h2>

        @include('modules.form', [
            'action'      => route('modules.store'),
            'method'      => 'POST',
            'module'      => null,
            'submitLabel' => __('adminlte::adminlte.save_information'),
        ])
    </div>
</div>
@endsection
