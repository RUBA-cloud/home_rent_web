{{-- resources/views/regions/_form.blade.php --}}
@php
    /**
     * Pass:
     *  - $action (string route)
     *  - $method ('POST'|'PUT'|'PATCH')
     *  - $region (Model|null)
     * Window broadcasting:
     *  - $broadcast = [
     *        'channel' => 'regions',
     *        'events'  => ['region_updated'],
     *    ];
     */
    $regionObj  = $region ?? null;
    $httpMethod = strtoupper($method ?? 'POST');

    $broadcast = $broadcast ?? [
        'channel' => 'regions',
        'events'  => ['region_updated'],
    ];
@endphp

<form id="regions-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'>
    @csrf
    @unless (in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($regionObj?->id))
        <input type="hidden" name="id" value="{{ $regionObj->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Country EN --}}
    <x-form.textarea
        id="country_en"
        name="country_en"
        label="{{ __('adminlte::adminlte.country') }} EN"
        :value="old('country_en', data_get($regionObj,'country_en',''))"
    />
    @error('country_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Country AR --}}
    <x-form.textarea
        id="country_ar"
        name="country_ar"
        label="{{ __('adminlte::adminlte.country') }} AR"
        dir="rtl"
        :value="old('country_ar', data_get($regionObj,'country_ar',''))"
    />
    @error('country_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- City EN --}}
    <x-form.textarea
        id="city_en"
        name="city_en"
        label="{{ __('adminlte::adminlte.city') }} EN"
        :value="old('city_en', data_get($regionObj,'city_en',''))"
    />
    @error('city_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- City AR --}}
    <x-form.textarea
        id="city_ar"
        name="city_ar"
        label="{{ __('adminlte::adminlte.city') }} AR"
        dir="rtl"
        :value="old('city_ar', data_get($regionObj,'city_ar',''))"
    />
    @error('city_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Expected Day Count (number) --}}
    <div class="form-group">
        <label for="excepted_day_count">{{ __('adminlte::adminlte.excepted_delivery_days') }}</label>
        <input
            id="excepted_day_count"
            type="number"
            min="0"
            class="form-control @error('excepted_day_count') is-invalid @enderror"
            name="excepted_day_count"
            value="{{ old('excepted_day_count', data_get($regionObj,'excepted_day_count','')) }}"
        >
        @error('excepted_day_count')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    {{-- Is Active --}}
    <div class="form-group" style="margin:20px 0;">
        <input type="hidden" name="is_active" value="0">
        @php $isActive = old('is_active', (int) data_get($regionObj,'is_active', 1)); @endphp
        <label>
            <input type="checkbox" name="is_active" value="1" {{ (int)$isActive ? 'checked' : '' }}>
            {{ __('adminlte::adminlte.is_active') }}
        </label>
    </div>
    @error('is_active') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    <x-adminlte-button
        label="{{ $httpMethod === 'POST'
            ? __('adminlte::adminlte.save_information')
            : __('adminlte::adminlte.update_information') }}"
        type="submit"
        theme="success"
        class="w-100"
        icon="fas fa-save"
    />
</form>

@push('js')
<script>
(function () {
  'use strict';

  const form = document.getElementById('regions-form');
  if (!form) {
    console.warn('[regions-form] form not found');
    return;
  }

  function applyPayloadToForm(payload) {
    if (!payload || typeof payload !== 'object') return;

    Object.entries(payload).forEach(([name, value]) => {
      const inputs = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
      if (!inputs.length) return;

      inputs.forEach((el) => {
        const type = (el.getAttribute('type') || el.tagName).toLowerCase();

        if (type === 'radio') {
          el.checked = (String(el.value) === String(value));
        } else if (type === 'checkbox') {
          el.checked = Boolean(value) && String(value) !== '0';
        } else {
          el.value = (value ?? '');
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    const ds          = form.dataset;
    const channelName = ds.channel || 'regions';

    let events;
    try {
      events = JSON.parse(ds.events || '["region_updated"]');
    } catch (_) {
      events = ['region_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['region_updated'];
    }

    // Optional: only update if ID matches current region being edited
    const currentIdInput = form.querySelector('input[name="id"]');
    const currentId      = currentIdInput ? currentIdInput.value : null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // Accept shapes: { payload: { region: {...} } }, { region: {...} }, or plain
        const raw = e?.payload || e?.region || e;
        const t   = raw?.region || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          // Different region → ignore
          return;
        }

        applyPayloadToForm(t);

        if (window.toastr) {
          toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }

        form.classList.add('border', 'border-success');
        setTimeout(() => form.classList.remove('border', 'border-success'), 800);
      };

      // Register for global bootstrapper
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // Subscribe immediately if AppBroadcast is already available
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[regions-form] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[regions-form] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
