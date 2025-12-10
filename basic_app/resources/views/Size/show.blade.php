@extends('adminlte::page')

@section('title', __('adminlte::adminlte.size'))

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
@endphp

<style>
  /* logical spacing works for LTR & RTL */
  .mie-1 { margin-inline-end: .25rem; }
  .mie-2 { margin-inline-end: .5rem; }
  .mis-2 { margin-inline-start: .5rem; }
</style>

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 fw-bold text-dark">
            @if ($isAr)
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.size') }}
            @else
                {{ __('adminlte::adminlte.size') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">

            {{-- Image --}}
            <div class="col-lg-4 col-md-5">
                <div class="border rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center p-2 h-100">
                    <img
                        src="{{ $size->image ? asset($size->image) : 'https://placehold.co/500x300?text=Size+Image' }}"
                        alt="{{ __('adminlte::adminlte.size') }} {{ __('adminlte::adminlte.image') }}"
                        class="img-fluid rounded-3"
                        style="max-height: 280px; object-fit: cover;"
                    >
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="size-name-en">{{ $size->name_en ?? '—' }}</div>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="size-name-ar">{{ $size->name_ar ?? '—' }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($size->is_active)
                            <span id="size-status-badge" class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle mie-1"></i>
                                <span id="size-status-text">{{ __('adminlte::adminlte.active') }}</span>
                            </span>
                        @else
                            <span id="size-status-badge" class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle mie-1"></i>
                                <span id="size-status-text">{{ __('adminlte::adminlte.inactive') }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.price') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="size-price">
                            {{ number_format((float) $size->price, 2) }} JD
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.descripation') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="size-description">
                            {{ $size->descripation ?? '—' }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('sizes.edit', $size->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit mie-2"></i>{{ __('adminlte::adminlte.edit') }}
                        </a>

                        <a href="{{ route('sizes.index') }}" class="btn btn-outline-secondary mis-2 px-4 py-2">
                            <i class="fas fa-arrow-left mie-2"></i>{{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>

{{-- Listener anchor for window broadcasting --}}
<div id="size-show-listener"
     data-channel="sizes"
     data-events='["size_updated","SizeUpdated"]'
     data-size-id="{{ $size->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('size-show-listener');
    if (!anchor) {
      console.warn('[size-show] listener anchor not found');
      return;
    }

    const channelName = anchor.dataset.channel || 'sizes';

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["size_updated"]');
    } catch (_) {
      events = ['size_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['size_updated'];
    }

    const currentId = anchor.dataset.sizeId || null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // accept shapes: { payload: { size: {...} } }, { size: {...} }, or plain
        const raw = e?.payload || e?.size || e;
        const t   = raw?.size || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          // different size → ignore
          return;
        }

        // Optional live DOM update (before reload)
        if (t.name_en !== undefined) {
          const el = document.getElementById('size-name-en');
          if (el) el.textContent = String(t.name_en ?? '—');
        }
        if (t.name_ar !== undefined) {
          const el = document.getElementById('size-name-ar');
          if (el) el.textContent = String(t.name_ar ?? '—');
        }
        if (t.price !== undefined) {
          const el = document.getElementById('size-price');
          if (el) el.textContent = `${Number(t.price || 0).toFixed(2)} JD`;
        }
        if (t.descripation !== undefined) {
          const el = document.getElementById('size-description');
          if (el) el.textContent = String(t.descripation ?? '—');
        }
        if (t.is_active !== undefined) {
          const badge = document.getElementById('size-status-badge');
          const text  = document.getElementById('size-status-text');
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

        // Reset Blade/page fully from server
        window.location.reload();
      };

      // register for global bootstrapper
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // subscribe immediately if AppBroadcast is ready
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[size-show] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[size-show] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
