{{-- resources/views/company_delivery/_form.blade.php --}}
{{-- expects: $action (string|Url), $method ('POST'|'PUT'|'PATCH'), optional $delivery (model|null) --}}
@php
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
@endphp

<form id="home_rent_feature-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="home_rent_feature"
      data-events='@json(["home_rent_feature_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endunless
 <x-upload-image
        :image="$homeRentFeature->image ?? null"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />
    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Country EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $homeRentFeature->name_en ?? '')"
        rows="1"
    />

    {{-- Country AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }} AR"
        dir="rtl"
        :value="old('name_ar', $homeRentFeature->name_ar ?? '')"
        rows="1"
    />
<x-form.textarea
        id="description_en"
        name="description_en"
        label="{{ __('adminlte::adminlte.description') }} EN"
        :value="old('description_en', $homeRentFeature->description_en ?? '')"
        rows="1"
    />
      <x-form.textarea
        id="description_ar"
        name="description_ar"
        label="{{ __('adminlte::adminlte.description') }} AR"
        dir="rtl"
        :value="old('description_ar', $homeRentFeature->description_ar ?? '')"
        rows="1"
    />
    {{-- Is Active (hidden 0 + checkbox 1) --}}
    <div class="form-group my-3">
        <input type="hidden" name="is_active" value="0">
        <label class="mb-0">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', (int) data_get($homeFeature, 'is_active', 1)) ? 'checked' : '' }}
            >
            {{ __('adminlte::adminlte.is_active') }}
        </label>
    </div>

      <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
</form>

@push('js')
@once
<script>
(function(){
  'use strict';

  // Reset helper (clears textareas/inputs; re-checks "is_active")
  function resetCompanyDeliveryForm() {
    const form = document.getElementById('home_rent_feature-form');
    if (!form) return;

    const nameEn = document.getElementById('name_en');
    const nameAr = document.getElementById('name_ar');

    if (nameEn) {
      nameEn.value = '';
      nameEn.dispatchEvent(new Event('input',  { bubbles: true }));
      nameEn.dispatchEvent(new Event('change', { bubbles: true }));
    }
    if (nameAr) {
      nameAr.value = '';
      nameAr.dispatchEvent(new Event('input',  { bubbles: true }));
      nameAr.dispatchEvent(new Event('change', { bubbles: true }));
    }

    const active = form.querySelector('input[name="is_active"][type="checkbox"]');
    if (active) {
      active.checked = true;
      active.dispatchEvent(new Event('change', { bubbles: true }));
    }

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_) {}
    }

    console.log('[company_delivery] form reset from broadcast');
  }

  // Optional: expose reset globally
  window.resetCompanyDeliveryForm = resetCompanyDeliveryForm;

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('company-delivery-form');
    if (!form) {
      console.warn('[company_delivery] form not found');
      return;
    }

    const channelName = form.dataset.channel || 'company_delivery';

    let events;
    try {
      events = JSON.parse(form.dataset.events || '["home_rent_feature_updated"]');
    } catch (_) {
      events = ['home_rent_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['home_rent_updated'];
    }

    // ---- Register with global broadcasting (like additional/category/branch) ----
    window.__pageBroadcasts = window.__pageBroadcasts || [];

    const handler = function (e) {
      // e may contain {delivery: {...}} in the future; for now we just reset
      resetCompanyDeliveryForm();
    };

    window.__pageBroadcasts.push({
      channel: 'home_rent_feature',          // broadcastOn()
      event:   'home_rent_feature_updated',  // broadcastAs()
      handler: handler
    });

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe('company_delivery', 'company_delivery_updated', handler);
      console.info('[company_delivery] subscribed via AppBroadcast → company_delivery / company_delivery_updated');
    } else {
      console.info('[company_delivery] registered in __pageBroadcasts; layout will subscribe later.');
    }

    // Optional manual trigger
    window.addEventListener('home-rent-feature:update', resetCompanyDeliveryForm);  });
})();
</script>
@endonce
@endpush
