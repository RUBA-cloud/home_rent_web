{{-- resources/views/permissions/edit.blade.php --}}
@extends('adminlte::page')

@php
    $isRtl = app()->isLocale('ar');
@endphp

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.permissions'))

@section('content_header')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="m-0">
                {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.permissions') }}
            </h1>
        </div>
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }} me-1"></i>
            {{ __('adminlte::adminlte.go_back') !== 'adminlte::adminlte.go_back'
                    ? __('adminlte::adminlte.go_back')
                    : __('Back') }}
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="far fa-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            {{-- IMPORTANT:
                 our partial `permissions/_form.blade.php` ALREADY has <form>...</form>
                 so we ONLY include it here and pass the data --}}
            @include('permissions.form', [
                'action'            => route('permissions.update', $permission),
                'method'            => 'PUT',
                'permission'        => $permission,
                'module'            => $modulesRow ?? null,
                'featuresForRadios' => $featuresForRadios ?? [],
                'defaultFeatureKey' => $defaultFeatureKey ?? null,
                'channel'           => 'permissions',
                'events'            => ['permissions_updated'],
            ])
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
/* keep any extra edit-page styles here if you need */
</style>
@endpush
