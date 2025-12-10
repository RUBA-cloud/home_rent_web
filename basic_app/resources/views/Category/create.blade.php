

 @extends('adminlte::page')
@section('title', __('adminlte::adminlte.category'))
@section('content')
<div class="container-fluid py-4" style="margin: 10px">
@section('content')
    <x-adminlte-card title="{{ __('adminlte::adminlte.module_category') }}">
        @include('Category.form', [
            'action'   => route('categories.store'),
            'method'   => 'POST',
            'category' => null,
        ]);
    </x-adminlte-card>;
@endsection
@endsection
