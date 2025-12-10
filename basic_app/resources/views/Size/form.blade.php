{{-- resources/views/sizes/_form.blade.php --}}
@php
    /**
     * Inputs:
     *  - $action (route)
     *  - $method ('POST'|'PUT'|'PATCH')
     *  - $size   (Model|null)
     * Optional:
     *  - $broadcast = [
     *        'channel' => 'sizes',
     *        'events'  => ['size_updated'],
     *    ];
     */
    $sizeObj    = $size ?? null;
    $httpMethod = strtoupper($method ?? 'POST');

    $broadcast = $broadcast ?? [
        'channel' => 'sizes',
        'events'  => ['size_updated'],
    ];
@endphp

<form id="sizes-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'>
    @csrf
    @unless (in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($sizeObj?->id))
        <input type="hidden" name="id" value="{{ $sizeObj->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Image --}}
    <x-upload-image
        :image="old('image', data_get($sizeObj,'image'))"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    {{-- Size Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', data_get($sizeObj,'name_en',''))"
    />
    @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Size Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', data_get($sizeObj,'name_ar',''))"
    />
    @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Price --}}
    <x-form.textarea
        id="price"
        name="price"
        label="{{ __('adminlte::adminlte.price') }}"
        :value="old('price', data_get($sizeObj,'price',''))"
        dir="rtl"
    />
    @error('price') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Description (field key is "descripation") --}}
    <x-form.textarea
        id="descripation"
        name="descripation"
        label="{{ __('adminlte::adminlte.descripation') }}"
        :value="old('descripation', data_get($sizeObj,'descripation',''))"
    />
    @error('descripation') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Is Active --}}
    <div class="form-group" style="margin: 20px 0;">
        <input type="hidden" name="is_active" value="0">
        @php $isActive = old('is_active', (int) data_get($sizeObj,'is_active', 1)); @endphp
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

  const form = document.getElementById('sizes-form');
  if (!form) {
    console.warn('[sizes-form] form not found');
    return;
  }

  function applyPayloadToForm(payload) {
    if (!payload || typeof payload !== 'object') return;

    Object.entries(payload).forEach(([name, value]) => {
      // don't try to set file inputs (image)
      const nodes = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
      if (!nodes.length) return;

      nodes.forEach((el) => {
        const type = (el.getAttribute('type') || el.tagName).toLowerCase();

        if (type === 'file') {
          return; // skip file inputs
        } else if (type === 'radio') {
          el.checked = (String(el.value) === String(value));
        } else if (type === 'checkbox') {
          el.checked = Boolean(value) && String(value) !== '0';
        } else {
          el.value = (value ?? '');
        }
      });
    });

    // little highlight
    form.classList.add('border', 'border-success');
    setTimeout(() => form.classList.remove('border', 'border-success'), 800);
  }

  document.addEventListener('DOMContentLoaded', function () {
    const ds          = form.dataset;
    const channelName = ds.channel || 'sizes';

    let events;
    try {
      events = JSON.parse(ds.events || '["size_updated"]');
    } catch (_) {
      events = ['size_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['size_updated'];
    }

    // Optional: only update if this specific size is being edited
    const currentIdInput = form.querySelector('input[name="id"]');
    const currentId      = currentIdInput ? currentIdInput.value : null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // Accept shapes: { payload: { size: {...} } }, { size: {...} }, or plain
        const raw = e?.payload || e?.size || e;
        const t   = raw?.size || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          // Different size → ignore
          return;
        }

        applyPayloadToForm(t);

        if (window.toastr) {
          toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }
      };

      // Register so your layout/global JS can hook it
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // If AppBroadcast is already set up, subscribe now
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[sizes-form] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[sizes-form] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
