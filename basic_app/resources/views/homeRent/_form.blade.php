{{-- resources/views/home_rent/_form.blade.php --}}
@php
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');

    $isAr = app()->isLocale('ar');

    $categoryError = $errors->has('category_id');

    $selectedCategoryId = old('category_id', $homeRent->category_id ?? null);

    // if $homeRent has a relation `features`, use that
    $selectedFeatureIds = collect(
        old(
            'home_rent_features',
            isset($homeRent) && isset($homeRent->features)
                ? $homeRent->features->pluck('id')->toArray()
                : []
        )
    );
@endphp

<form id="home-rent-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="home_rent"
      data-events='@json(["home_rent_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endunless

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

    {{-- IMAGE --}}
    <x-upload-image
        :image="$homeRent->image ?? null"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    {{-- Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $homeRent->name_en ?? '')"
        rows="1"
    />

    {{-- Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }} AR"
        dir="rtl"
        :value="old('name_ar', $homeRent->name_ar ?? '')"
        rows="1"
    />

    {{-- Description EN --}}
    <x-form.textarea
        id="description_en"
        name="description_en"
        label="{{ __('adminlte::adminlte.description') }} EN"
        :value="old('description_en', $homeRent->description_en ?? '')"
        rows="2"
    />

    {{-- Longitude --}}
    <x-form.textarea
        id="longitude"
        name="longitude"
        label="{{ __('adminlte::adminlte.longitude') }}"
        :value="old('longitude', $homeRent->longitude ?? '')"
        rows="1"
    />

    {{-- Latitude --}}
    <x-form.textarea
        id="latitude"
        name="latitude"
        label="{{ __('adminlte::adminlte.latitude') }}"
        :value="old('latitude', $homeRent->latitude ?? '')"
        rows="1"
    />

    {{-- Bedrooms --}}
    <x-form.textarea
        id="number_of_bedrooms"
        name="number_of_bedrooms"
        label="{{ __('adminlte::adminlte.bedrooms') }}"
        :value="old('number_of_bedrooms', $homeRent->number_of_bedrooms ?? '')"
        rows="1"
    />

    {{-- Bathrooms --}}
    <x-form.textarea
        id="number_of_bathrooms"
        name="number_of_bathrooms"
        label="{{ __('adminlte::adminlte.bathrooms') }}"
        :value="old('number_of_bathrooms', $homeRent->number_of_bathrooms ?? '')"
        rows="1"
    />

    <x-form.textarea
        id="rent_price"
        name="rent_price"
        label="{{ __('adminlte::adminlte.rent_price') }}"
        :value="old('rent_price', $homeRent->rent_price ?? '')"
        rows="1"
    />

    {{-- ✅ FIX: price value كان غلط --}}
    <x-form.textarea
        id="price"
        name="price"
        label="{{ __('adminlte::adminlte.price') }}"
        :value="old('price', $homeRent->price ?? '')"
        rows="1"
    />

    {{-- Description AR --}}
    <x-form.textarea
        id="description_ar"
        name="description_ar"
        label="{{ __('adminlte::adminlte.description') }} AR"
        dir="rtl"
        :value="old('description_ar', $homeRent->description_ar ?? '')"
        rows="2"
    />

        <x-form.textarea
        id="address_ar"
        name="address_ar"
        label="{{ __('adminlte::adminlte.address') }} AR"
        dir="rtl"
        :value="old('description_ar', $homeRent->description_ar ?? '')"
        rows="2"
    />
      <x-form.textarea
        id="address_en"
        name="address_en"
        label="{{ __('adminlte::adminlte.address') }} EN"
        dir="rtl"
        :value="old('address_en', $homeRent->description_ar ?? '')"
        rows="2"
    />
    {{-- VIDEO --}}
    <div class="form-group">
        <label for="video">{{ __('adminlte::adminlte.video') }}</label>
        <input
            type="file"
            name="video"
            id="video"
            class="form-control @error('video') is-invalid @enderror"
            accept="video/mp4,video/webm,video/ogg"
        >
        @error('video')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
    </div>

    {{-- CATEGORY SELECT --}}
    <div class="form-group">
        <label for="category_id">
            {{ __('adminlte::adminlte.category') }}
            <span class="text-danger">*</span>
        </label>
        <select
            id="category_id"
            name="category_id"
            class="form-control select2 custom-select2 {{ $categoryError ? 'is-invalid' : '' }}"
            required
            data-placeholder="{{ __('adminlte::adminlte.select') . ' ' . __('adminlte::adminlte.category') }}"
            style="width: 100%;">

            <option value="" disabled {{ $selectedCategoryId ? '' : 'selected' }}>
                {{ __('adminlte::adminlte.select') . ' ' . __('adminlte::adminlte.category') }}
            </option>

            @forelse($categories as $category)
                <option value="{{ $category->id }}"
                    {{ (int) $selectedCategoryId === (int) $category->id ? 'selected' : '' }}>
                    {{ $isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar) }}
                </option>
            @empty
                <option value="" disabled>
                    {{ __('adminlte::adminlte.no_records') }}
                </option>
            @endforelse
        </select>

        @error('category_id')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
    </div>

    {{-- ✅ HOME RENT FEATURES MULTI SELECT --}}
    <div class="form-group">
        <label for="features_select">
            {{ __('adminlte::adminlte.home_rent_feature') }}
        </label>

        <select
            id="features_select"
            name="home_rent_features[]"
            class="form-control select2 custom-select2
                   @error('home_rent_features') is-invalid @enderror
                   @error('home_rent_features.*') is-invalid @enderror"
            multiple
            data-placeholder="{{ __('adminlte::adminlte.select') . ' ' . __('adminlte::adminlte.home_rent_feature') }}"
            style="width: 100%;">

            @forelse($homeFeatures as $feature)
                <option value="{{ $feature->id }}"
                    {{ $selectedFeatureIds->contains((int) $feature->id) ? 'selected' : '' }}>
                    {{ $isAr ? ($feature->name_ar ?? $feature->name_en) : ($feature->name_en ?? $feature->name_ar) }}
                </option>
            @empty
                <option value="" disabled>
                    {{ __('adminlte::adminlte.no_records') }}
                </option>
            @endforelse
        </select>

        @error('home_rent_features')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
        @error('home_rent_features.*')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- Is Active (hidden 0 + checkbox 1) --}}
    <div class="form-group my-3">
        <input type="hidden" name="is_active" value="0">
        <label class="mb-0">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', (int) data_get($homeRent ?? null, 'is_active', 1)) ? 'checked' : '' }}
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

  function resetHomeRentForm() {
    const form = document.getElementById('home-rent-form');
    if (!form) return;

    const idsToClear = [
      'name_en',
      'name_ar',
      'description_en',
      'description_ar',
      'longitude',
      'latitude',
      'video',
      'image',
      'category_id',
      'number_of_bedrooms',
      'number_of_bathrooms',
      'price',
      'rent_price'
    ];

    idsToClear.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;

      if (el.type === 'file') {
        el.value = '';
        return;
      }

      el.value = '';
      el.dispatchEvent(new Event('input',  { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
    });

    // select2 resets
    const category = document.getElementById('category_id');
    const features = document.getElementById('features_select');

    if (category && window.$) $(category).val(null).trigger('change');
    if (features && window.$) $(features).val([]).trigger('change');

    // reset checkbox
    const active = form.querySelector('input[name="is_active"][type="checkbox"]');
    if (active) {
      active.checked = true;
      active.dispatchEvent(new Event('change', { bubbles: true }));
    }

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_) {}
    }

    console.log('[home_rent] form reset from broadcast');
  }

  window.resetHomeRentForm = resetHomeRentForm;

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('home-rent-form');
    if (!form) {
      console.warn('[home_rent] form not found');
      return;
    }

    const channelName = form.dataset.channel || 'home_rent';

    let events;
    try { events = JSON.parse(form.dataset.events || '["home_rent_updated"]'); }
    catch (_) { events = ['home_rent_updated']; }

    if (!Array.isArray(events) || !events.length) events = ['home_rent_updated'];

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    const handler = function () { resetHomeRentForm(); };

    window.__pageBroadcasts.push({
      channel: channelName,
      event:   'home_rent_updated',
      handler: handler
    });

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe(channelName, 'home_rent_updated', handler);
      console.info('[home_rent] subscribed via AppBroadcast → ' + channelName + ' / home_rent_updated');
    } else {
      console.info('[home_rent] registered in __pageBroadcasts; layout will subscribe later.');
    }

    window.addEventListener('home-rent:update', resetHomeRentForm);
  });
})();
</script>
@endonce
@endpush
