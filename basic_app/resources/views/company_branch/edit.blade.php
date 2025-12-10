@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.branches'))

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="flex: 1; padding: 2rem;">
@include('company_branch.form', [
            'action'     => route('companyBranch.update', $branch->id),
            'method'     => 'PUT',
            'branch' => $branch,
        ])
    </div>
</div>
@endsection
