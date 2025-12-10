{{-- resources/views/offers/_form.blade.php --}}
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)

@php
    $isAr = app()->getLocale() === 'ar';
    /** @var \App\Models\Offer|null $offer */
    $offer = $offer ?? null;

    // Ensure we have an int-only collection of selected category ids
    $oldCategoryIds = collect(old('category_ids', $offer?->categories?->pluck('id')->all() ?? []))
        ->map(fn ($v) => (int) $v)
        ->filter()
        ->values();

    // AdminLTE date config (Tempus Dominus)
    $config = ['format' => 'DD/MM/YYYY'];

    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');

    // Fallbacks for $action/$method if not injected
    /** @var string $action */
    $action = $action ?? url()->current();
    /** @var string $method */
    $method = strtoupper($method ?? ($offer?->exists ? 'PUT' : 'POST'));
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="offer-form"
      data-channel="offers"
      data-events='@json(["offer_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array($method, ['GET','POST']))
        @method($method)
    @endunless

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="padding: 24px; width: 100%;">
        <div class="card-body">

            {{-- Name EN --}}
            <div class="form-group">
                <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
                <input id="name_en" type="text" name="name_en" class="form-control"
                       value="{{ old('name_en', $offer->name_en ?? '') }}" required>
            </div>

            {{-- Name AR --}}
            <div class="form-group">
                <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
                <input id="name_ar" type="text" name="name_ar" class="form-control"
                       value="{{ old('name_ar', $offer->name_ar ?? '') }}" required>
            </div>

            {{-- Description EN --}}
            <div class="form-group">
                <label for="description_en">
                    {{ __('adminlte::adminlte.descripation') }} (EN)
                </label>
                <textarea id="description_en" name="description_en" class="form-control" rows="3" required>{{ old('description_en', $offer->description_en ?? '') }}</textarea>
            </div>

            {{-- Description AR --}}
            <div class="form-group">
                <label for="description_ar">
                    {{ __('adminlte::adminlte.descripation') }} (AR)
                </label>
                <textarea id="description_ar" name="description_ar" class="form-control" rows="3" required>{{ old('description_ar', $offer->description_ar ?? '') }}</textarea>
            </div>

            {{-- Categories (multi-select) --}}
            <div class="form-group">
                <label for="category_ids">{{ __('adminlte::adminlte.category') }}</label>
                <select id="category_ids" name="category_ids[]" class="form-control select2" multiple required style="width:100%;">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $oldCategoryIds->contains((int)$category->id) ? 'selected' : '' }}>
                            {{ $isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Offer Type (single select) --}}
            <div class="form-group">
                <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
                <select id="type_id" name="type_id" class="form-control select2" required style="width:100%;">
                    <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                    @foreach($offerTypes as $type)
                        <option value="{{ $type->id }}" {{ (string)old('type_id', $offer->type_id ?? '') === (string)$type->id ? 'selected' : '' }}>
                            {{ $isAr ? ($type->name_ar ?? $type->name_en) : ($type->name_en ?? $type->name_ar) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dates --}}
            <div class="form-group">
                <label for="start_date">{{ __('adminlte::adminlte.start_date') }}</label>
                <x-adminlte-input-date name="start_date" :config="$config" id="start_date"
                                       placeholder="{{ __('adminlte::adminlte.choose_date') }}"
                                       value="{{ old('start_date', $offer->start_date ?? '') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="Set to Today" data-today="start_date"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="form-group">
                <label for="end_date">{{ __('adminlte::adminlte.end_date') }}</label>
                <x-adminlte-input-date name="end_date" :config="$config" id="end_date"
                                       placeholder="{{ __('adminlte::adminlte.choose_date') }}"
                                       value="{{ old('end_date', $offer->end_date ?? '') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="Set to Today" data-today="end_date"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            {{-- Active --}}
            <div class="form-check mb-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                       {{ old('is_active', (int)($offer->is_active ?? 1)) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

            {{-- Submit --}}
            <div class="form-group">
                <x-adminlte-button
                    :label="isset($offer) ? __('adminlte::adminlte.update_information') : __('adminlte::adminlte.save_information')"
                    type="submit"
                    theme="success"
                    class="w-100"
                    icon="fas fa-save"
                />
            </div>
        </div>
    </div>
</form>

@push('js')
@once
<script>
(function(){
  'use strict';

  const esc = (s) => {
    try { return (window.CSS && CSS.escape) ? CSS.escape(s) : String(s).replace(/"/g,'\\"'); }
    catch(_) { return String(s); }
  };

  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setDate = (name, value) => setField(name, value);

  const setCheckbox = (name, isOn) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(isOn);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setMultiSelect = (selectorOrName, values = []) => {
    const el = document.querySelector(
      selectorOrName && selectorOrName.startsWith('#')
        ? selectorOrName
        : `[name="${esc(selectorOrName)}[]"]`
    );
    if (!el) return;
    const want = (values || []).map(v => String(v));
    Array.from(el.options).forEach(opt => { opt.selected = want.includes(String(opt.value)); });
    if (window.jQuery && jQuery(el).hasClass('select2')) {
      jQuery(el).trigger('change.select2');
    } else {
      el.dispatchEvent(new Event('change', { bubbles: true }));
    }
  };

  // Apply broadcast payload
  const applyPayload = (payload) => {
    const o = payload?.offer ?? payload ?? {};

    setField('name_en', o.name_en);
    setField('name_ar', o.name_ar);
    setField('description_en', o.description_en);
    setField('description_ar', o.description_ar);

    const categoryIds = o.category_ids || (Array.isArray(o.categories) ? o.categories.map(c => c.id) : []);
    setMultiSelect('#category_ids', categoryIds);

    setField('type_id', o.type_id);
    if (window.jQuery && jQuery('#type_id').hasClass('select2')) {
      jQuery('#type_id').val(o.type_id ?? '').trigger('change.select2');
    }

    setDate('start_date', o.start_date);
    setDate('end_date',   o.end_date);

    setCheckbox('is_active', o.is_active);

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_) {}
    }

    console.log('[offers] form updated from broadcast', o);
  };

  // Optional global reset
  window.resetOfferForm = function(){
    setField('name_en',''); setField('name_ar','');
    setField('description_en',''); setField('description_ar','');
    setMultiSelect('#category_ids', []);
    if (window.jQuery && jQuery('#type_id').hasClass('select2')) {
      jQuery('#type_id').val('').trigger('change.select2');
    } else {
      setField('type_id','');
    }
    setDate('start_date',''); setDate('end_date','');
    setCheckbox('is_active', 1);
  };

  document.addEventListener('DOMContentLoaded', () => {
    // Initialize Select2 if available
    if (window.jQuery) {
      jQuery('#category_ids').select2({ width: '100%', theme: 'bootstrap4', placeholder: '{{ __('adminlte::adminlte.select') }}' });
      jQuery('#type_id').select2({ width: '100%', theme: 'bootstrap4', placeholder: '{{ __('adminlte::adminlte.select') }}' });
    }

    // “Set to Today” quick buttons (uses DD/MM/YYYY)
    document.querySelectorAll('[data-today]').forEach(btn => {
      btn.addEventListener('click', (ev) => {
        ev.preventDefault();
        const id = btn.getAttribute('data-today');
        if (!id) return;
        const today = new Date();
        const dd = String(today.getDate()).padStart(2,'0');
        const mm = String(today.getMonth()+1).padStart(2,'0');
        const yyyy = today.getFullYear();
        setDate(id, `${dd}/${mm}/${yyyy}`);
      });
    });

    const form = document.getElementById('offer-form');
    if (!form) {
      console.warn('[offers] form not found');
      return;
    }

    // Register with global broadcasting system
    window.__pageBroadcasts = window.__pageBroadcasts || [];

    let events;
    try {
      events = JSON.parse(form.dataset.events || '["offer_updated"]');
    } catch (_) {
      events = ['offer_updated'];
    }
    if (!Array.isArray(events) || !events.length) events = ['offer_updated'];

    const handler = function (e) {
      applyPayload(e && (e.offer ?? e));
    };

    window.__pageBroadcasts.push({
      channel: form.dataset.channel || 'offers', // broadcastOn()
      event:   events[0] || 'offer_updated',     // broadcastAs()
      handler: handler
    });

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      (events || ['offer_updated']).forEach(evt => {
        window.AppBroadcast.subscribe(form.dataset.channel || 'offers', evt, handler);
      });
      console.info('[offers] subscribed via AppBroadcast →', form.dataset.channel, events);
    } else {
      console.info('[offers] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endonce
@endpush

@section('css')
<style>
  /* small cosmetic tweaks */
  #offer-form .select2-container--bootstrap4 .select2-selection--single,
  #offer-form .select2-container--bootstrap4 .select2-selection--multiple {
    min-height: calc(2.25rem + 2px);
  }
  #offer-form .select2-container--bootstrap4 .select2-selection__rendered {
    line-height: 2.25rem;
  }
  #offer-form .select2-container--bootstrap4 .select2-selection__arrow {
    height: 2.25rem;
  }
</style>
@endsection
