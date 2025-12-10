{{-- filepath: /Users/rubahammad/Desktop/basic_app3/basic_app/resources/views/components/action_buttons.blade.php --}}
@props([
    'label' => __('adminlte::adminlte.actions'), // default label text
    'addRoute' => null,
    'historyRoute' => null,
    'showAdd' => true,
    'historyParams' => null,
    'goBack'=>true
])

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    {{-- Label on the left --}}
            @if($showAdd && $addRoute)

    <h5 class="fw-bold text-primary m-0">
        <i class="fas fa-tasks me-2"></i> {{ $label }}
    </h5>
@endif
    {{-- Buttons on the right --}}
    <div style="display: flex; gap: 12px;">
        @if($showAdd && $addRoute)
            <a href="{{ route($addRoute) }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> {{ __('adminlte::adminlte.add') }}
            </a>
        @else
        @if(!empty($goBack))
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">

    <a href="{{ route($historyRoute, $historyParams ?? []) }}"
       class="btn btn-secondary btn-sm btn-goback" style="align-items: end;">
      <i class="fas fa-arrow-left me-1"></i> {{ __('adminlte::adminlte.go_back') }}
    </a>
  </div>
@endif



@endif
        @if($historyRoute && $showAdd )
            <a href="{{ route($historyRoute, true) }}" class="btn btn-info">
                <i class="fas fa-history me-1"></i> {{ __('adminlte::adminlte.view_history') }}
            </a>
        @endif
    </div>
</div>
