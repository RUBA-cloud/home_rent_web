@extends('adminlte::page')
@section('title', "Edit Order #{$order->id}")

@section('content')
@if($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('orders.update',$order) }}">
  @csrf @method('PUT')

  <div class="card mb-3">
    <div class="card-header"><strong>Order</strong></div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes',$order->notes) }}</textarea>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Status</label>
          <select name="status" id="status" class="form-select">
            <option value="0" @selected(old('status',$order->status)==0)>{{ __('adminlte::adminlte.pending') }}
            </option>
            <option value="1" @selected(old('status',$order->status)==1)>{{ __('adminlte::adminlte.accepted') }}</option>
            <option value="2" @selected(old('status',$order->status)==2)>{{ __('adminlte::adminlte.rejected') }}</option>
            <option value="3" @selected(old('status',$order->status)==3)>{{ __('adminlte::adminlte.completed') }}</option>
          </select>
        </div>
        <div class="col-md-8" id="employeeContainer" style="display:none;">
          <label class="form-label">Assign Employee (required when Accepting)</label>
          <select name="employee_id" class="form-select">
            <option value="">-- choose --</option>
            @foreach($employees as $emp)
              <option value="{{ $emp->id }}" @selected(old('employee_id',$order->employee_id)==$emp->id)>{{ $emp->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="card-footer d-flex gap-2">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-secondary" href="{{ route('orders.show',$order) }}">Cancel</a>
    </div>
  </div>
</form>

<script>
  const statusSel = document.getElementById('status');
  const box = document.getElementById('employeeContainer');
  function toggleEmployee(){ box.style.display = (statusSel.value === '1') ? '' : 'none'; }
  statusSel.addEventListener('change', toggleEmployee); toggleEmployee();
</script>
@endsection
