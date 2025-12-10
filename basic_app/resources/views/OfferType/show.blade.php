@extends('adminlte::page')

@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.offers_type'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">

        <x-adminlte-card title="{{ __('adminlte::adminlte.offers_type') }}" theme="info" icon="fas fa-info-circle" collapsible>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.name_en')}}:</strong>
                    <div id="offer-type-name-en" class="form-control-plaintext">{{ $offerType->name_en ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.name_ar')}}:</strong>
                    <div id="offer-type-name-ar" class="form-control-plaintext">{{ $offerType->name_ar ?? '-' }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.descripation')}} (EN):</strong>
                    <div id="offer-type-description-en" class="form-control-plaintext">{{ $offerType->description_en ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.descripation')}} (AR):</strong>
                    <div id="offer-type-description-ar" class="form-control-plaintext">{{ $offerType->description_ar ?? '-' }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>{{ __('adminlte::adminlte.is_discount') }}</strong><br>
                    <span id="offer-type-is-discount" class="badge {{ $offerType->is_discount ? 'bg-success' : 'bg-secondary' }}">
                        {{ $offerType->is_discount ? __('Yes') : __('No') }}
                    </span>
                </div>
                <div class="col-md-4">
                    <strong>{{__('adminlte::adminlte.is_total_gift')}}</strong><br>
                    <span id="offer-type-is-total-gift" class="badge {{ $offerType->is_total_gift ? 'bg-success' : 'bg-secondary' }}">
                        {{ $offerType->is_total_gift ? __('Yes') : __('No') }}
                    </span>
                </div>
                <div class="col-md-4">
                    <strong>{{__('adminlte::adminlte.is_product_count_gift')}}</strong><br>
                    <span id="offer-type-is-product-count-gift" class="badge {{ $offerType->is_product_count_gift ? 'bg-success' : 'bg-secondary' }}">
                        {{ $offerType->is_product_count_gift ? __('Yes') : __('No') }}
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <strong>{{ __('Active') }}:</strong><br>
                <span id="offer-type-active" class="badge {{ $offerType->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $offerType->is_active ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
                </span>
            </div>

            @if($offerType->is_discount)
                <x-adminlte-card id="offer-type-discount-card"
                                 title="{{ __('adminlte::adminlte.discount') }} {{ __('adminlte::adminlte.details')}}"
                                 theme="lightblue"
                                 icon="fas fa-percent"
                                 class="mb-3">
                    <div class="mb-2">
                        <strong>{{ __('adminlte::adminlte.discount')}}:</strong>
                        <div id="offer-type-discount-value-product" class="form-control-plaintext">
                            {{ $offerType->discount_value_product ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <strong>{{ __('Discount Value Delivery') }}:</strong>
                        <div id="offer-type-discount-value-delivery" class="form-control-plaintext">
                            {{ $offerType->discount_value_delivery ?? '-' }}
                        </div>
                    </div>
                </x-adminlte-card>
            @endif

            @if($offerType->is_product_count_gift || $offerType->is_total_gift)
                <x-adminlte-card id="offer-type-gift-card"
                                 title="{{ __('adminlte::adminlte.gift') }} {{ __('adminlte::adminlte.details')}}"
                                 theme="lightblue"
                                 icon="fas fa-gift"
                                 class="mb-3">
                    @if($offerType->is_product_count_gift)
                        <div class="mb-2">
                            <strong>{{ __('adminlte::adminlte.product_count_gift') }}:</strong>
                            <div id="offer-type-products-count-gift" class="form-control-plaintext">
                                {{ $offerType->products_count_to_get_gift_offer ?? '-' }}
                            </div>
                        </div>
                    @endif
                    @if($offerType->is_total_gift)
                        <div>
                            <strong>{{ __('adminlte::adminlte.total_gift') }}:</strong>
                            <div id="offer-type-total-gift" class="form-control-plaintext">
                                {{ $offerType->total_gift ?? '-' }}
                            </div>
                        </div>
                    @endif
                </x-adminlte-card>
            @endif

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('offers_type.edit', $offerType->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> {{ __('adminlte::adminlte.edit') }}
                </a>
            </div>

        </x-adminlte-card>

    </div>
</div>

{{-- Listener anchor for broadcasting --}}
<div id="offer-type-listener"
     data-channel="offers_type"
     data-events='["offer_type_updated","OfferTypeUpdated"]'
     data-offer-type-id="{{ $offerType->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  function norm(v) {
    if (v === undefined || v === null) return '';
    return String(v);
  }

  function setBadge(id, isOn, yesText, noText) {
    const el = document.getElementById(id);
    if (!el) return;
    const on = !!Number(isOn);
    el.classList.remove('bg-success', 'bg-secondary', 'bg-danger');
    el.classList.add(on ? 'bg-success' : 'bg-secondary');
    el.textContent = on ? yesText : noText;
  }

  function updateDomFromPayload(payload) {
    if (!payload) return;

    const t = payload.offerType ?? payload.offer_type ?? payload ?? {};

    // Only update if the payload belongs to this offer type
    const anchor = document.getElementById('offer-type-listener');
    const currentId = anchor ? anchor.dataset.offerTypeId : null;
    if (currentId && t.id && String(t.id) !== String(currentId)) {
      return;
    }

    // Basic fields
    const nameEnEl = document.getElementById('offer-type-name-en');
    if (nameEnEl) nameEnEl.textContent = norm(t.name_en) || '-';

    const nameArEl = document.getElementById('offer-type-name-ar');
    if (nameArEl) nameArEl.textContent = norm(t.name_ar) || '-';

    const descEnEl = document.getElementById('offer-type-description-en');
    if (descEnEl) descEnEl.textContent = norm(t.description_en) || '-';

    const descArEl = document.getElementById('offer-type-description-ar');
    if (descArEl) descArEl.textContent = norm(t.description_ar) || '-';

    // Boolean badges
    if (t.is_discount !== undefined) {
      setBadge('offer-type-is-discount', t.is_discount, '{{ __("Yes") }}', '{{ __("No") }}');
    }
    if (t.is_total_gift !== undefined) {
      setBadge('offer-type-is-total-gift', t.is_total_gift, '{{ __("Yes") }}', '{{ __("No") }}');
    }
    if (t.is_product_count_gift !== undefined) {
      setBadge('offer-type-is-product-count-gift', t.is_product_count_gift, '{{ __("Yes") }}', '{{ __("No") }}');
    }
    if (t.is_active !== undefined) {
      const el = document.getElementById('offer-type-active');
      if (el) {
        const on = !!Number(t.is_active);
        el.classList.remove('bg-success', 'bg-secondary', 'bg-danger');
        el.classList.add(on ? 'bg-success' : 'bg-secondary');
        el.textContent = on
          ? '{{ __("adminlte::adminlte.active") }}'
          : '{{ __("adminlte::adminlte.inactive") }}';
      }
    }

    // Discount values (if card exists in DOM)
    const discProdEl = document.getElementById('offer-type-discount-value-product');
    if (discProdEl && t.discount_value_product !== undefined) {
      discProdEl.textContent = norm(t.discount_value_product) || '-';
    }
    const discDelivEl = document.getElementById('offer-type-discount-value-delivery');
    if (discDelivEl && t.discount_value_delivery !== undefined) {
      discDelivEl.textContent = norm(t.discount_value_delivery) || '-';
    }

    // Gift values (if card exists in DOM)
    const countGiftEl = document.getElementById('offer-type-products-count-gift');
    if (countGiftEl && t.products_count_to_get_gift_offer !== undefined) {
      countGiftEl.textContent = norm(t.products_count_to_get_gift_offer) || '-';
    }
    const totalGiftEl = document.getElementById('offer-type-total-gift');
    if (totalGiftEl && t.total_gift !== undefined) {
      totalGiftEl.textContent = norm(t.total_gift) || '-';
    }

    if (window.toastr) {
      toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
    }

    console.log('[offer_type show] updated from broadcast payload', t);
  }

  // Optional global helper
  window.updateOfferTypeShow = updateDomFromPayload;

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('offer-type-listener');
    if (!anchor) {
      console.warn('[offer_type show] listener anchor not found');
      return;
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["offer_type_updated"]');
    } catch (_) {
      events = ['offer_type_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['offer_type_updated'];
    }

    const handler = function (e) {
      updateDomFromPayload(e && (e.offerType ?? e.offer_type ?? e));
    };

    window.__pageBroadcasts.push({
      channel: 'offers_type',        // broadcastOn()
      event:   'offer_type_updated', // broadcastAs()
      handler: handler
    });

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe('offers_type', 'offer_type_updated', handler);
      console.info('[offer_type show] subscribed via AppBroadcast → offers_type / offer_type_updated');
    } else {
      console.info('[offer_type show] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endpush
