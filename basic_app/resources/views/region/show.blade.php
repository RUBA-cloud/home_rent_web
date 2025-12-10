@extends('adminlte::page')

@section('title', __('adminlte::adminlte.region'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <h2 class="h4 mb-0 text-dark fw-bold">
        <i class="fas fa-code-branch me-2 text-primary"></i>
        @if (app()->getLocale() === 'ar')
            {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.regions') }}
        @else
            {{ __('adminlte::adminlte.regions') }} {{ __('adminlte::adminlte.details') }}
        @endif
    </h2>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm mt-3">
        <div class="row g-4">

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Country EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.country') }} EN</small>
                        <div class="fs-5 fw-bold text-dark" id="region-country-en">{{ $region->country_en }}</div>
                    </div>

                    {{-- Country AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.country') }} AR</small>
                        <div class="fs-5 fw-bold text-dark" id="region-country-ar">{{ $region->country_ar }}</div>
                    </div>

                    {{-- City EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.city') }} EN</small>
                        <div class="fs-5 fw-bold text-dark" id="region-city-en">{{ $region->city_en }}</div>
                    </div>

                    {{-- City AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.city') }} AR</small>
                        <div class="fs-5 fw-bold text-dark" id="region-city-ar">{{ $region->city_ar }}</div>
                    </div>

                    {{-- Expected day count --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.excepted_delivery_days') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="region-excepted-days">{{ $region->excepted_day_count }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($region->is_active)
                            <span id="region-status-badge" class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i>
                                <span id="region-status-text">{{ __('adminlte::adminlte.active') }}</span>
                            </span>
                        @else
                            <span id="region-status-badge" class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i>
                                <span id="region-status-text">{{ __('adminlte::adminlte.inactive') }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('regions.edit', $region->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('regions.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>

{{-- Listener anchor for window broadcasting --}}
<div id="region-show-listener"
     data-channel="regions"
     data-events='["region_updated","RegionUpdated"]'
     data-region-id="{{ $region->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('region-show-listener');
    if (!anchor) {
      console.warn('[region-show] listener anchor not found');
      return;
    }

    const channelName = anchor.dataset.channel || 'regions';

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["region_updated"]');
    } catch (_) {
      events = ['region_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['region_updated'];
    }

    const currentId = anchor.dataset.regionId || null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // Accept shapes: { payload: { region: {...} } }, { region: {...} }, or plain
        const raw = e?.payload || e?.region || e;
        const t   = raw?.region || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          // Different region → ignore
          return;
        }

        // Optional small DOM update (in case you later want to avoid full reload)
        if (t.country_en !== undefined) {
          const el = document.getElementById('region-country-en');
          if (el) el.textContent = String(t.country_en ?? '');
        }
        if (t.country_ar !== undefined) {
          const el = document.getElementById('region-country-ar');
          if (el) el.textContent = String(t.country_ar ?? '');
        }
        if (t.city_en !== undefined) {
          const el = document.getElementById('region-city-en');
          if (el) el.textContent = String(t.city_en ?? '');
        }
        if (t.city_ar !== undefined) {
          const el = document.getElementById('region-city-ar');
          if (el) el.textContent = String(t.city_ar ?? '');
        }
        if (t.excepted_day_count !== undefined) {
          const el = document.getElementById('region-excepted-days');
          if (el) el.textContent = String(t.excepted_day_count ?? '');
        }
        if (t.is_active !== undefined) {
          const badge = document.getElementById('region-status-badge');
          const text  = document.getElementById('region-status-text');
          const on    = !!Number(t.is_active);
          if (badge) {
            badge.classList.remove('bg-success', 'bg-danger');
            badge.classList.add(on ? 'bg-success' : 'bg-danger');
          }
          if (text) {
            text.textContent = on
              ? '{{ __("adminlte::adminlte.active") }}'
              : '{{ __("adminlte::adminlte.inactive") }}';
          }
        }

        if (window.toastr) {
          toastr.info(@json(__('adminlte::adminlte.saved_successfully')));
        }

        // Reset page → reload to pull fresh data from server
        window.location.reload();
      };

      // Register for global bootstrapper
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // Subscribe immediately if AppBroadcast is available
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[region-show] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[region-show] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
