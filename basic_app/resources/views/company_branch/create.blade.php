

 @extends('adminlte::page')
@section('title', __('adminlte::adminlte.company_branch'))
@section('content')
<div class="container-fluid py-4" style="margin: 10px">
    <x-adminlte-card class="header_card"
        title="{{ __('adminlte::adminlte.company_branch') }}"
        icon="fas fa-building" collapsible maximizable>
    </div>
    @include('company_branch.form', [
        'action'     => route('companyBranch.store'),
        'method'     => 'POST',
        'branch' => null
    ]);

    </x-adminlte-card>
</div>
@endsection
