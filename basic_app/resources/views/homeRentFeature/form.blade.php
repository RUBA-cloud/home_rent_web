{{-- resources/views/home_rent_feature/_form.blade.php --}}
@php
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
@endphp

<form id="home-rent-feature-form"
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

    {{-- Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $homeRentFeature->name_en ?? '')"
        rows="1"
    />

    {{-- Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }} AR"
        dir="rtl"
        :value="old('name_ar', $homeRentFeature->name_ar ?? '')"
        rows="1"
    />

    {{-- Description EN --}}
    <x-form.textarea
        id="description_en"
        name="description_en"
        label="{{ __('adminlte::adminlte.description') }} EN"
        :value="old('description_en', $homeRentFeature->description_en ?? '')"
        rows="2"
    />

    {{-- Description AR --}}
    <x-form.textarea
        id="description_ar"
        name="description_ar"
        label="{{ __('adminlte::adminlte.description') }} AR"
        dir="rtl"
        :value="old('description_ar', $homeRentFeature->description_ar ?? '')"
        rows="2"
    />

    {{-- Is Active (hidden 0 + checkbox 1) --}}
    <div class="form-group my-3">
        <input type="hidden" name="is_active" value="0">
        <label class="mb-0">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', (int) data_get($homeRentFeature ?? null, 'is_active', 1)) ? 'checked' : '' }}
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
(function () {
  'use strict';

  // Reset helper (clears fields & re-checks "is_active")
  function resetHomeRentFeatureForm() {
    const form = document.getElementById('home-rent-feature-form');
    if (!form) return;

    const nameEn        = document.getElementById('name_en');
    const nameAr        = document.getElementById('name_ar');
    const descEn        = document.getElementById('description_en');
    const descAr        = document.getElementById('description_ar');
    const imageInput    = document.getElementById('image');
    const activeCheckbox = form.querySelector('input[name="is_active"][type="checkbox"]');

    // Text fields
    [nameEn, nameAr, descEn, descAr].forEach((el) => {
      if (!el) return;
      el.value = '';
      el.dispatchEvent(new Event('input',  { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
    });

    // Checkbox
    if (activeCheckbox) {
      activeCheckbox.checked = true;
      activeCheckbox.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Clear file input (best effort)
    if (imageInput) {
      imageInput.value = '';
    }

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch (_) {}
    }

    console.log('[home_rent_feature] form reset from broadcast');
  }

  // Optional: expose reset globally
  window.resetHomeRentFeatureForm = resetHomeRentFeatureForm;

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('home-rent-feature-form');
    if (!form) {
      console.warn('[home_rent_feature] form not found');
      return;
    }

    const channelName = form.dataset.channel || 'home_rent_feature';

    let events;
    try {
      events = JSON.parse(form.dataset.events || '["home_rent_feature_updated"]');
    } catch (_) {
      events = ['home_rent_feature_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['home_rent_feature_updated'];
    }

    // ---- Register with global broadcasting array (for layout-level subscription) ----
    window.__pageBroadcasts = window.__pageBroadcasts || [];

    const handler = function (e) {
      // e may contain payload later; for now we just reset the form
      resetHomeRentFeatureForm();
    };

    window.__pageBroadcasts.push({
      channel: channelName,                // broadcastOn()
      event:   'home_rent_feature_updated', // broadcastAs()
      handler: handler
    });

    // If you already have a global AppBroadcast (like other modules)
    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe(channelName, 'home_rent_feature_updated', handler);
      console.info('[home_rent_feature] subscribed via AppBroadcast → ' + channelName + ' / home_rent_feature_updated');
    } else {
      console.info('[home_rent_feature] registered in __pageBroadcasts; layout will subscribe later.');
    }

    // Optional manual trigger (you can dispatch this from anywhere)
    window.addEventListener('home-rent-feature:update', resetHomeRentFeatureForm);
  });
})();
</script>
@endonce
@endpush
