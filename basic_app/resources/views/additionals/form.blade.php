@php
  // Consistent defaults (still OK even if listener is centralized)
  $broadcast = $broadcast ?? [
      'channel'        => 'additional',
      'events'         => ['additional_updated'],
      'pusher_key'     => env('PUSHER_APP_KEY'),
      'pusher_cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
  ];
@endphp

<form id="additional-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] ?? 'additional' }}"
      data-events='@json($broadcast['events'] ?? ["additional_updated"])'
      data-pusher-key="{{ $broadcast['pusher_key'] ?? '' }}"
      data-pusher-cluster="{{ $broadcast['pusher_cluster'] ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET','POST']))
        @method($method)
    @endunless

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-upload-image
        :image="$additional->image ?? null"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    <x-form.textarea id="name_en" name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $additional->name_en ?? '')" rows="1" />

    <x-form.textarea id="name_ar" name="name_ar" dir="rtl"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        :value="old('name_ar', $additional->name_ar ?? '')" rows="1" />

    <x-form.textarea id="price" name="price"
        label="{{ __('adminlte::adminlte.price') }}"
        :value="old('price', $additional->price ?? '')" rows="1" />

    <x-form.textarea id="description" name="description"
        label="{{ __('adminlte::adminlte.descripation') }}"
        :value="old('description', $additional->description ?? '')" />

    <div class="form-group my-3">
        <input type="hidden" name="is_active" value="0">
        <label class="mb-0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', (int) ($additional->is_active ?? 1)) ? 'checked' : '' }}>
            {{ __('adminlte::adminlte.is_active') }}
        </label>
    </div>

    <x-adminlte-button
        :label="isset($additional) ? __('adminlte::adminlte.update_information') : __('adminlte::adminlte.save_information')"
        type="submit" theme="success" class="w-100" icon="fas fa-save" />
</form>

@push('js')
@once
<script>
document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  // ---------- Helpers (like company_info) ----------
  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;

  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setCheckbox = (name, isOn) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(isOn);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const previewImage = (url) => {
    if (!url) return;
    const img = document.querySelector('#image-preview,[data-role="image-preview"]');
    if (img) img.src = url;
  };

  // ---------- Apply payload → reset/patch form ----------
  const applyPayload = (payload) => {
    // Accept both {additional: {...}} and flat payload
    const a = (payload && (payload.additional ?? payload)) || {};

    setField('name_en',     a.name_en);
    setField('name_ar',     a.name_ar);
    setField('price',       a.price);
    setField('description', a.description);
    setCheckbox('is_active', a.is_active);

    previewImage(a.image_url || a.image);

    if (window.bsCustomFileInput && document.querySelector('input[type="file"][name="image"]')) {
      try { bsCustomFileInput.init(); } catch (_) {}
    }

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch (_) {}
    }

    console.log('[additional] patched additional form from payload', a);
  };

  // Optional: expose a hard reset (clear all fields)
  window.resetAdditionalForm = function(){
    setField('name_en', '');
    setField('name_ar', '');
    setField('price', '');
    setField('description', '');
    setCheckbox('is_active', 1);
    if (window.bsCustomFileInput && document.querySelector('input[type="file"][name="image"]')) {
      try { bsCustomFileInput.init(); } catch (_) {}
    }
    console.log('[additional] form reset manually');
  };

  // ---------- Register with global broadcasting (like company_info) ----------
  window.__pageBroadcasts = window.__pageBroadcasts || [];
  window.__pageBroadcasts.push({
    channel: 'additional',           // must match broadcastOn()
    event:   'additional_updated',   // must match broadcastAs()
    handler: applyPayload
  });

  // If your layout already created AppBroadcast, subscribe now
  if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
    window.AppBroadcast.subscribe('additional', 'additional_updated', applyPayload);
    console.info('[additional] subscribed via AppBroadcast → additional / additional_updated');
  } else {
    console.info('[additional] registered in __pageBroadcasts; layout will subscribe later.');
  }
});
</script>
@endonce
@endpush
