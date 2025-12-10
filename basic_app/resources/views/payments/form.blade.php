{{-- resources/views/payment/_form.blade.php --}}
@php
    /**
     * Expected variables:
     * - $payment  (Payment|null)
     * - $action   (string route name, e.g. 'payment.store' or 'payment.update')
     * - $method   (string 'POST'|'PUT'|'PATCH') – default POST
     */

    /** @var \App\Models\Payment|null $payment */
    $payment = $payment ?? null;
    $method  = strtoupper($method ?? 'POST');

    $isEdit  = $payment && $payment->exists;
@endphp

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-money-check-alt me-2 text-primary"></i>

            @if (app()->getLocale() === 'ar')
                {{ $isEdit
                    ? __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.payment')
                    : __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.payment') }}
            @else
                {{ $isEdit
                    ? __('adminlte::adminlte.payment') . ' ' . __('adminlte::adminlte.edit')
                    : __('adminlte::adminlte.payment') . ' ' . __('adminlte::adminlte.create') }}
            @endif
        </h2>

        <div>
            <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
            </a>
        </div>
    </div>

    {{-- Card with form --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">

        <form method="POST"
              action="{{ $isEdit ? route($action, $payment->id) : route($action) }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="row g-4">
                <div class="col-12">
                    <div class="row gy-3">

                        {{-- Name EN --}}
                        <div class="col-md-6">
                            <label for="name_en" class="form-label fw-semibold">
                                {{ __('adminlte::adminlte.name_en') }}
                            </label>
                            <input type="text"
                                   id="name_en"
                                   name="name_en"
                                   class="form-control @error('name_en') is-invalid @enderror"
                                   value="{{ old('name_en', $payment->name_en ?? '') }}"
                                   required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Name AR --}}
                        <div class="col-md-6">
                            <label for="name_ar" class="form-label fw-semibold">
                                {{ __('adminlte::adminlte.name_ar') }}
                            </label>
                            <input type="text"
                                   id="name_ar"
                                   name="name_ar"
                                   class="form-control @error('name_ar') is-invalid @enderror"
                                   value="{{ old('name_ar', $payment->name_ar ?? '') }}"
                                   required>
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block mb-2" style="margin: 5px">
                                {{ __('adminlte::adminlte.is_active') }}
                            </label>

                            @php
                                $activeOld = old('is_active', isset($payment) ? (int)$payment->is_active : 1);
                            @endphp

                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="radio"
                                       name="is_active"
                                       id="is_active_yes"
                                       style="margin: 5px"
                                       value="1"
                                       {{ (string)$activeOld === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active_yes">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    {{ __('adminlte::adminlte.active') }}
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" style="margin: 5px"
                                       type="radio"
                                       name="is_active"

                                       id="is_active_no"
                                       value="0"
                                       {{ (string)$activeOld === '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active_no">
                                    <i class="fas fa-times-circle text-danger me-1"></i>
                                    {{ __('adminlte::adminlte.inactive') }}
                                </label>
                            </div>

                            @error('is_active')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary px-4">
                    {{ __('adminlte::adminlte.cancel') }}
                </a>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i>
                    {{ $isEdit
                        ? __('adminlte::adminlte.update')
                        : __('adminlte::adminlte.save') }}
                </button>
            </div>
        </form>

    </x-adminlte-card>
</div>
