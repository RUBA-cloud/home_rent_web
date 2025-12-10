@extends('adminlte::page')

@section('title', __('adminlte::adminlte.details') . ' ' . __('adminlte::adminlte.offers'))

@section('content_header')
    <h1 class="mb-2">{{ __('adminlte::adminlte.offers') }}</h1>
@stop

@section('content')
<div class="card shadow" style="margin: 5px;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title text-primary mb-0">
            <i class="fas fa-info-circle"></i>
            {{ __('adminlte::adminlte.details') }}:
            <strong id="offer-name-en-header">{{ $offer->name_en ?? '-' }}</strong>
        </h3>
        <a href="{{ route('offers.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('adminlte::adminlte.go_back') }}
        </a>
    </div>

    <div class="card-body">
        @php $isAr = app()->getLocale() === 'ar'; @endphp

        {{-- Names --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.name_en') }}</label>
                <div id="offer-name-en" class="font-weight-bold">{{ $offer->name_en ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</label>
                <div id="offer-name-ar" class="font-weight-bold">{{ $offer->name_ar ?? '-' }}</div>
            </div>
        </div>

        {{-- Descriptions --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                <div id="offer-description-en" class="text-wrap">
                    {!! nl2br(e($offer->description_en ?? '')) ?: '-' !!}
                </div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <div id="offer-description-ar" class="text-wrap">
                    {!! nl2br(e($offer->description_ar ?? '')) ?: '-' !!}
                </div>
            </div>
        </div>

        {{-- Category & Type --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.category') }}</label>
                <div id="offer-categories">
                    @forelse(($offer->categories ?? []) as $category)
                        <span class="badge badge-info mr-1">
                            {{ $isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar) }}
                        </span>
                    @empty
                        <span class="text-muted">-</span>
                    @endforelse
                </div>
            </div>

            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.type') }}</label>
                <div id="offer-type" class="font-weight-bold">
                    {{ $isAr ? ($offer->type->name_ar ?? $offer->type->name_en ?? '-') : ($offer->type->name_en ?? $offer->type->name_ar ?? '-') }}
                </div>
            </div>
        </div>

        {{-- Dates --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.start_date') }}</label>
                <div id="offer-start-date">
                    {{ $offer->start_date ? \Illuminate\Support\Carbon::parse($offer->start_date)->format('Y-m-d') : '-' }}
                </div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.end_date') }}</label>
                <div id="offer-end-date">
                    {{ $offer->end_date ? \Illuminate\Support\Carbon::parse($offer->end_date)->format('Y-m-d') : '-' }}
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.is_active') }}</label>
                <div>
                    <span id="offer-status"
                          class="badge {{ !empty($offer->is_active) ? 'badge-success' : 'badge-danger' }}">
                        @if(!empty($offer->is_active))
                            {{ __('adminlte::adminlte.active') }}
                        @else
                            {{ __('adminlte::adminlte.inactive') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Listener anchor for broadcasting --}}
<div id="offer-listener"
     data-channel="offers"
     data-events='["offer_updated","OfferUpdated"]'
     data-offer-id="{{ $offer->id }}">
</div>
@stop

@push('js')
<script>
(function () {
  'use strict';

  function norm(v) {
    if (v === undefined || v === null) return '';
    return String(v);
  }

  function renderCategories(container, categories) {
    if (!container) return;
    if (!Array.isArray(categories) || categories.length === 0) {
      container.innerHTML = '<span class="text-muted">-</span>';
      return;
    }

    const isAr = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
    container.innerHTML = '';
    categories.forEach(c => {
      const span = document.createElement('span');
      span.className = 'badge badge-info mr-1';
      const nameEn = norm(c.name_en || '');
      const nameAr = norm(c.name_ar || '');
      span.textContent = isAr
        ? (nameAr || nameEn || ('Category #' + c.id))
        : (nameEn || nameAr || ('Category #' + c.id));
      container.appendChild(span);
    });
  }

  function updateDomFromPayload(payload) {
    if (!payload) return;
    const o = payload.offer ?? payload.offers ?? payload ?? {};

    // Ensure same offer
    const anchor = document.getElementById('offer-listener');
    const currentId = anchor ? anchor.dataset.offerId : null;
    if (currentId && o.id && String(o.id) !== String(currentId)) {
      return;
    }

    // Names
    const nameEnHeader = document.getElementById('offer-name-en-header');
    if (nameEnHeader) nameEnHeader.textContent = norm(o.name_en) || '-';

    const nameEnEl = document.getElementById('offer-name-en');
    if (nameEnEl) nameEnEl.textContent = norm(o.name_en) || '-';

    const nameArEl = document.getElementById('offer-name-ar');
    if (nameArEl) nameArEl.textContent = norm(o.name_ar) || '-';

    // Descriptions
    const descEnEl = document.getElementById('offer-description-en');
    if (descEnEl) descEnEl.textContent = norm(o.description_en) || '';

    const descArEl = document.getElementById('offer-description-ar');
    if (descArEl) descArEl.textContent = norm(o.description_ar) || '';

    // Categories
    const catContainer = document.getElementById('offer-categories');
    if (Array.isArray(o.categories) || Array.isArray(o.category_ids)) {
      // Expecting o.categories as array of objects from broadcast
      const cats = Array.isArray(o.categories)
        ? o.categories
        : [];
      renderCategories(catContainer, cats);
    }

    // Type
    const typeEl = document.getElementById('offer-type');
    if (typeEl && o.type) {
      const isAr = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
      const nameEn = norm(o.type.name_en || '');
      const nameAr = norm(o.type.name_ar || '');
      typeEl.textContent = isAr
        ? (nameAr || nameEn || '-')
        : (nameEn || nameAr || '-');
    }

    // Dates
    const startEl = document.getElementById('offer-start-date');
    if (startEl && o.start_date !== undefined) {
      startEl.textContent = norm(o.start_date) || '-';
    }

    const endEl = document.getElementById('offer-end-date');
    if (endEl && o.end_date !== undefined) {
      endEl.textContent = norm(o.end_date) || '-';
    }

    // Status
    const statusEl = document.getElementById('offer-status');
    if (statusEl && o.is_active !== undefined && o.is_active !== null) {
      const on = !!Number(o.is_active);
      statusEl.classList.remove('badge-success', 'badge-danger');
      statusEl.classList.add(on ? 'badge-success' : 'badge-danger');
      statusEl.textContent = on
        ? '{{ __("adminlte::adminlte.active") }}'
        : '{{ __("adminlte::adminlte.inactive") }}';
    }

    if (window.toastr) {
      toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
    }

    console.log('[offers show] updated from broadcast payload', o);
  }

  // Optional global helper
  window.updateOfferShow = updateDomFromPayload;

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('offer-listener');
    if (!anchor) {
      console.warn('[offers show] listener anchor not found');
      return;
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["offer_updated"]');
    } catch (_) {
      events = ['offer_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['offer_updated'];
    }

    const handler = function (e) {
      updateDomFromPayload(e && (e.offer ?? e.offers ?? e));
    };

    window.__pageBroadcasts.push({
      channel: 'offers',        // broadcastOn()
      event:   'offer_updated', // broadcastAs()
      handler: handler
    });

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe('offers', 'offer_updated', handler);
      console.info('[offers show] subscribed via AppBroadcast → offers / offer_updated');
    } else {
      console.info('[offers show] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endpush
