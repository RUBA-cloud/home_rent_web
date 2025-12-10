@extends('adminlte::page')
@section('title', 'Dashboard')
@section('content_header')
    <h1>{{ __('adminlte::adminlte.Dashboard') }}</h1>
@stop
@section('content')
<livewire:dashboard />
@endsection


