{{-- resources/views/type/_form.blade.php --}}
@php
    /**
     * Inputs:
     *  - $action (route)
     *  - $method ('POST'|'PUT'|'PATCH')
     *  - $type   (Model|null)
     * Optional:
     *  - $broadcast = [
     *        'channel' => 'types',
     *        'events'  => ['type_updated'],
     *    ];
     */
    $typeObj    = $type ?? null;
    $httpMethod = strtoupper($method ?? 'POST');

    $broadcast = $broadcast ?? [
        'channel' => 'types',
        'events'  => ['type_updated'],
    ];
@endphp

<form id="type-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'>
    @csrf
    @unless(in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($typeObj?->id))
        <input type="hidden" name="id" value="{{ $typeObj->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Type Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', data_get($typeObj,'name_en',''))"
    />
    @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Type Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', data_get($typeObj,'name_ar',''))"
    />
    @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Is Active --}}
    <div class="form-group" style="margin: 20px 0;">
        <input type="hidden" name="is_active" value="0">
        @php $isActive = old('is_active', (int) data_get($typeObj,'is_active', 1)); @endphp
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

  const form = document.getElementById('type-form');
  if (!form) {
    console.warn('[type-form] form not found');
    return;
  }

  function applyPayloadToForm(payload) {
    if (!payload || typeof payload !== 'object') return;

    Object.entries(payload).forEach(([name, value]) => {
      const nodes = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
      if (!nodes.length) return;

      nodes.forEach((el) => {
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

    form.classList.add('border', 'border-success');
    setTimeout(() => form.classList.remove('border', 'border-success'), 800);
  }

  document.addEventListener('DOMContentLoaded', function () {
    const ds          = form.dataset;
    const channelName = ds.channel || 'types';

    let events;
    try {
      events = JSON.parse(ds.events || '["type_updated"]');
    } catch (_) {
      events = ['type_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['type_updated'];
    }

    // Optional: only react for the current type
    const currentIdInput = form.querySelector('input[name="id"]');
    const currentId      = currentIdInput ? currentIdInput.value : null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // shapes: { payload: { type: {...} } }, { type: {...} }, or plain
        const raw = e?.payload || e?.type || e;
        const t   = raw?.type || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          return; // different type → ignore
        }

        applyPayloadToForm(t);

        if (window.toastr) {
          toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }
      };

      // register with global broadcaster bootstrap
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // subscribe immediately if AppBroadcast exists
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[type-form] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[type-form] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
