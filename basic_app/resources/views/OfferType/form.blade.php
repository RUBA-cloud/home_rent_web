<div>
    <!-- It always seems impossible until it is done. - Nelson Mandela -->
</div>

{{-- resources/views/offers_type/_form.blade.php --}}
{{-- expects:
    $action (string|Url),
    $method ('POST'|'PUT'|'PATCH'),
    optional $offersType (model|null)
    optional $categories (Collection) -> id, name_en/name_ar
    optional $products   (Collection) -> id, name
--}}

@php($ot = $offersType ?? null)
@php
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="offers-type-form"
      data-channel="offers_type"
      data-events='@json(["offer_type_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET','POST']))
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
            {{-- names --}}
            <div class="mb-3">
                <label for="name_en" class="form-label">{{ __('adminlte::adminlte.name_en') }}</label>
                <input type="text" name="name_en" id="name_en" class="form-control"
                       value="{{ old('name_en', $ot->name_en ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="name_ar" class="form-label">{{ __('adminlte::adminlte.name_ar') }}</label>
                <input type="text" name="name_ar" id="name_ar" class="form-control"
                       value="{{ old('name_ar', $ot->name_ar ?? '') }}" required>
            </div>

            {{-- descriptions --}}
            <div class="mb-3">
                <label for="description_en" class="form-label">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en', $ot->description_en ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="description_ar" class="form-label">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <textarea name="description_ar" id="description_ar" class="form-control" dir="rtl">{{ old('description_ar', $ot->description_ar ?? '') }}</textarea>
            </div>

            {{-- MODE FLAGS (pick one) --}}
            <div class="form-check mb-2">
                <input type="checkbox" name="is_discount" id="is_discount" class="form-check-input" value="1"
                       {{ old('is_discount', (int)($ot->is_discount ?? 0)) ? 'checked' : '' }}>
                <label for="is_discount" class="form-check-label">{{ __('adminlte::adminlte.is_discount') }}</label>
            </div>

            <div class="form-check mb-2">
                <input type="checkbox" name="is_total_gift" id="is_total_gift" class="form-check-input" value="1"
                       {{ old('is_total_gift', (int)($ot->is_total_gift ?? 0)) ? 'checked' : '' }}>
                <label for="is_total_gift" class="form-check-label">{{ __('adminlte::adminlte.is_total_gift') }}</label>
            </div>

            <div class="form-check mb-2">
                <input type="checkbox" name="is_product_count_gift" id="is_product_count_gift" class="form-check-input" value="1"
                       {{ old('is_product_count_gift', (int)($ot->is_product_count_gift ?? 0)) ? 'checked' : '' }}>
                <label for="is_product_count_gift" class="form-check-label">{{ __('adminlte::adminlte.is_product_count_gift') }}</label>
            </div>

            <div class="form-check mb-2">
                <input type="checkbox" name="is_total_discount" id="is_total_discount" class="form-check-input" value="1"
                       {{ old('is_total_discount', (int)($ot->is_total_discount ?? 0)) ? 'checked' : '' }}>
                <label for="is_total_discount" class="form-check-label">{{ __('adminlte::adminlte.is_total_discount') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                       {{ old('is_active', (int)($ot->is_active ?? 1)) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

            {{-- DISCOUNT FIELDS (show when is_discount) --}}
            <div id="discount_fields" style="display: none;">
                <div class="mb-3">
                    <label for="discount_value_product" class="form-label">{{ __('adminlte::adminlte.discount_value_product') }}</label>
                    <input type="number" step="0.01" name="discount_value_product" id="discount_value_product" class="form-control"
                           value="{{ old('discount_value_product', $ot->discount_value_product ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="discount_value_delivery" class="form-label">{{ __('adminlte::adminlte.discount_value_delivery') }}</label>
                    <input type="number" step="0.01" name="discount_value_delivery" id="discount_value_delivery" class="form-control"
                           value="{{ old('discount_value_delivery', $ot->discount_value_delivery ?? '') }}">
                </div>
            </div>

            {{-- TOTAL DISCOUNT FIELD (show when is_total_discount) --}}
            <div id="total_discount_field" style="display: none;">
                <div class="mb-3">
                    <label for="total_discount" class="form-label">{{ __('adminlte::adminlte.total_amount') }}</label>
                    <input type="number" step="0.01" name="total_discount" id="total_discount" class="form-control"
                           value="{{ old('total_discount', $ot->total_discount ?? '') }}">
                </div>
            </div>

            {{-- GIFT FIELDS (show when is_total_gift || is_product_count_gift) --}}
            <div id="gift_fields" style="display: none;">
                {{-- select category --}}
                <div class="mb-3">
                    <label for="gift_category_id" class="form-label">{{ __('adminlte::adminlte.category') }}</label>
                    <select name="gift_category_id" id="gift_category_id" class="form-control">
                        <option value="">{{ __('adminlte::adminlte.choose_file') }}</option>
                        @if(!empty($categories))
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ (string)old('gift_category_id', $ot->gift_category_id ?? '') === (string)$cat->id ? 'selected' : '' }}>
                                    {{ $cat->name_ar ?? $cat->name_en ?? $cat->name ?? 'Category #'.$cat->id }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- select gift product --}}
                <div class="mb-3">
                    <label for="gift_product_id" class="form-label">{{ __('adminlte::adminlte.product') }}</label>
                    <select name="gift_product_id" id="gift_product_id" class="form-control">
                        <option value="">{{ __('adminlte::adminlte.choose_file') }}</option>
                        @if(!empty($products))
                            @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                    {{ (string)old('gift_product_id', $ot->gift_product_id ?? '') === (string)$p->id ? 'selected' : '' }}>
                                    {{ $p->name ?? ('Product #'.$p->id) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- how many products to buy to get gift --}}
                <div class="mb-3">
                    <label for="products_count_to_get_gift_offer" class="form-label">{{ __('adminlte::adminlte.products_count_to_get_gift_offer') }}</label>
                    <input type="number" name="products_count_to_get_gift_offer" id="products_count_to_get_gift_offer" class="form-control"
                           value="{{ old('products_count_to_get_gift_offer', $ot->products_count_to_get_gift_offer ?? '') }}">
                </div>

                {{-- how many gifts / total gift --}}
                <div class="mb-3">
                    <label for="total_gift" class="form-label">{{ __('adminlte::adminlte.total_gift') }}</label>
                    <input type="number" name="total_gift" id="total_gift" class="form-control"
                           value="{{ old('total_gift', $ot->total_gift ?? '') }}">
                </div>
            </div>

            <x-adminlte-button
                :label="isset($ot) ? __('adminlte::adminlte.update_information') : __('adminlte::adminlte.save_information')"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
        </div>
    </div>
</form>

@push('js')
@once
<script>
(function(){
  'use strict';

  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;
  const get = (sel) => document.querySelector(sel);

  function toggleFields() {
    const isDiscount       = get('#is_discount');
    const isTotalGift      = get('#is_total_gift');
    const isProductGift    = get('#is_product_count_gift');
    const isTotalDiscount  = get('#is_total_discount');

    const discountBox      = get('#discount_fields');
    const giftBox          = get('#gift_fields');
    const totalDiscountBox = get('#total_discount_field');

    if (discountBox)      discountBox.style.display = 'none';
    if (giftBox)          giftBox.style.display = 'none';
    if (totalDiscountBox) totalDiscountBox.style.display = 'none';

    if (isDiscount && isDiscount.checked) {
      if (discountBox) discountBox.style.display = 'block';
      return;
    }

    if ((isTotalGift && isTotalGift.checked) || (isProductGift && isProductGift.checked)) {
      if (giftBox) giftBox.style.display = 'block';
      return;
    }

    if (isTotalDiscount && isTotalDiscount.checked) {
      if (totalDiscountBox) totalDiscountBox.style.display = 'block';
      return;
    }
  }

  function toggleCheckboxes() {
    const modes = [
      get('#is_discount'),
      get('#is_total_gift'),
      get('#is_product_count_gift'),
      get('#is_total_discount'),
    ].filter(Boolean);

    modes.forEach(cb => cb.disabled = false);

    const selected = modes.find(cb => cb.checked);
    if (selected) {
      modes.forEach(cb => {
        if (cb !== selected) cb.disabled = true;
      });
    }
  }

  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setCheck = (name, on) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(on);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  // apply broadcast payload
  const applyPayload = (payload) => {
    const o = payload?.offers_type ?? payload?.offerType ?? payload ?? {};

    setField('name_en', o.name_en);
    setField('name_ar', o.name_ar);
    setField('description_en', o.description_en);
    setField('description_ar', o.description_ar);

    setCheck('is_discount', o.is_discount);
    setCheck('is_total_gift', o.is_total_gift);
    setCheck('is_total_discount', o.is_total_discount);
    setCheck('is_product_count_gift', o.is_product_count_gift);
    setCheck('is_active', o.is_active);

    setField('discount_value_product', o.discount_value_product);
    setField('discount_value_delivery', o.discount_value_delivery);
    setField('total_discount', o.total_discount);

    setField('products_count_to_get_gift_offer', o.products_count_to_get_gift_offer);
    setField('total_gift', o.total_gift);
    setField('gift_category_id', o.gift_category_id);
    setField('gift_product_id', o.gift_product_id);

    toggleFields();
    toggleCheckboxes();

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_) {}
    }
    console.log('[offers_type] form updated from broadcast', o);
  };

  // Optional hard reset
  window.resetOffersTypeForm = function () {
    setField('name_en', '');
    setField('name_ar', '');
    setField('description_en', '');
    setField('description_ar', '');
    setCheck('is_discount', 0);
    setCheck('is_total_gift', 0);
    setCheck('is_total_discount', 0);
    setCheck('is_product_count_gift', 0);
    setCheck('is_active', 1);
    setField('discount_value_product', '');
    setField('discount_value_delivery', '');
    setField('total_discount', '');
    setField('products_count_to_get_gift_offer', '');
    setField('total_gift', '');
    setField('gift_category_id', '');
    setField('gift_product_id', '');
    toggleFields();
    toggleCheckboxes();
  };

  document.addEventListener('DOMContentLoaded', () => {
    toggleFields();
    toggleCheckboxes();

    ['#is_discount','#is_total_gift','#is_product_count_gift','#is_total_discount']
      .forEach(sel => {
        const el = get(sel);
        if (el) {
          el.addEventListener('change', function(){
            if (this.checked) {
              ['#is_discount','#is_total_gift','#is_product_count_gift','#is_total_discount']
                .forEach(s2 => {
                  const el2 = get(s2);
                  if (el2 && el2 !== this) {
                    el2.checked = false;
                  }
                });
            }
            toggleFields();
            toggleCheckboxes();
          });
        }
      });

    const form = document.getElementById('offers-type-form');
    if (!form) {
      console.warn('[offers_type] form not found');
      return;
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    let events;
    try {
      events = JSON.parse(form.dataset.events || '["offer_type_updated"]');
    } catch (_) {
      events = ['offer_type_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['offer_type_updated'];
    }

    const handler = function (e) {
      applyPayload(e && (e.offers_type ?? e.offerType ?? e));
    };

    window.__pageBroadcasts.push({
      channel: 'offers_type',        // broadcastOn()
      event:   'offer_type_updated', // broadcastAs()
      handler: handler
    });

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe('offers_type', 'offer_type_updated', handler);
      console.info('[offers_type] subscribed via AppBroadcast → offers_type / offer_type_updated');
    } else {
      console.info('[offers_type] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endonce
@endpush
