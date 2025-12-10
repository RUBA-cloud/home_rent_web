@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.category'))

{{-- فعّل بلجن Select2 المدمج مع AdminLTE --}}
@section('plugins.Select2', true)

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card p-4 w-100 shadow-sm">
        <h2 class="mb-4" style="font-size: 1.8rem; font-weight: 700; color: #22223B;">
            {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.category') }}
        </h2>
        @include('Category.form', [
            'action'     => route('categories.update', $category->id),
            'method'     => 'PUT',
            'category' => $category
        ])
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        // Initialize Select2 (AdminLTE plugin already loads the assets)
        $('.select2').select2({ width: '100%' });
    });
</script>
@endpush
