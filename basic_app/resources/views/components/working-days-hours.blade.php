@props([
    'branch_working_days'      => [], // array | csv | json
    'branch_working_hours_from'=> '',
    'branch_working_hours_to'  => '',
    'branch'                   => null,
])

@php
    $daysOfWeek = [
        'sat' => __('adminlte::adminlte.saturday'),
        'sun' => __('adminlte::adminlte.sunday'),
        'mon' => __('adminlte::adminlte.monday'),
        'tue' => __('adminlte::adminlte.tuesday'),
        'wed' => __('adminlte::adminlte.wednesday'),
        'thu' => __('adminlte::adminlte.thursday'),
        'fri' => __('adminlte::adminlte.friday'),
    ];

    // Normalize working days (accept array, CSV string, or JSON)
    $workingDays = old('working_days', $branch?->working_days ?? $branch_working_days);

    if (is_string($workingDays)) {
        $decoded = json_decode($workingDays, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $workingDays = $decoded;
        } else {
            $workingDays = array_filter(array_map('trim', explode(',', $workingDays)));
        }
    }

    if (!is_array($workingDays)) {
        $workingDays = [];
    } else {
        $workingDays = array_values($workingDays);
    }

    // Map translated labels back to day keys if needed
    $labelToKey = array_flip($daysOfWeek);
    $workingDays = array_map(fn($v) => $labelToKey[$v] ?? $v, $workingDays);

    // Working hours (time-only; default values)
    $workingHoursFrom = old('working_hours_from', $branch?->working_hours_from ?? $branch_working_hours_from) ?: '09:00';
    $workingHoursTo   = old('working_hours_to',   $branch?->working_hours_to   ?? $branch_working_hours_to)   ?: '17:00';

    $isAr = app()->getLocale() === 'ar';
@endphp

{{-- Working Days --}}
<div class="mb-3">
    <label class="form-label fw-bold">{{ __('adminlte::adminlte.working_days') }}</label>
    <div class="d-flex flex-wrap gap-2">
        @foreach ($daysOfWeek as $key => $label)
            <div class="form-check d-inline-flex align-items-center me-3 mb-2 @if($isAr) ms-3 me-0 @endif" style="gap:.5rem;">
                <input
                    class="form-check-input m-0"
                    type="checkbox"
                    name="working_days[]"
                    value="{{ $label }}"
                    id="day_{{ $key }}"
                    {{ in_array($key, $workingDays, true) ? 'checked' : '' }}>
                <label class="form-check-label m-4" for="day_{{ $key }}">
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>
</div>

{{-- Working Hours (Time Picker) --}}
<div class="mb-4">
    <label class="form-label fw-semibold">{{ __('adminlte::adminlte.working_time') }}</label>
    <div class="row g-3">
        <div class="col-md-6">
            <x-adminlte-input
                name="working_hours_from_visible"
                id="branch_working_hours_from_visible"
                label="{{ __('adminlte::adminlte.from') }}"
                placeholder="HH:mm"
                value="{{ $workingHoursFrom }}"
                type="text"
                igroup-size="lg"
                fgroup-class="mb-3"
            />
            <input type="hidden" id="branch_working_hours_from" name="working_hours_from" value="{{ $workingHoursFrom }}">
        </div>

        <div class="col-md-6">
            <x-adminlte-input
                name="working_hours_to_visible"
                id="branch_working_hours_to_visible"
                label="{{ __('adminlte::adminlte.to') }}"
                placeholder="HH:mm"
                value="{{ $workingHoursTo }}"
                type="text"
                igroup-size="lg"
                fgroup-class="mb-3"
            />
            <input type="hidden" id="branch_working_hours_to" name="working_hours_to" value="{{ $workingHoursTo }}">
        </div>
    </div>
</div>

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@if($isAr)
<style>
    #branch_working_hours_from_visible,
    #branch_working_hours_to_visible {
        text-align: right;
    }
</style>
@endif
@endpush
@push('js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@if($isAr)
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
@endif

@endpush

