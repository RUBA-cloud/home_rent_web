{{-- resources/views/additional/show.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.additional'))

@php
    /**
     * Expected:
     *  - $additional (App\Models\Additional)
     *  - optional $broadcast (override channel/event)
     */

    $broadcast = $broadcast ?? [
        'channel'        => 'additional',
        'events'         => ['additional_updated'],
        'pusher_key'     => env('PUSHER_APP_KEY'),
        'pusher_cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
    ];
@endphp

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-plus-circle me-2 text-primary"></i>
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.additional') }}
            @else
                {{ __('adminlte::adminlte.additional') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>

        <div class="d-flex">
            {{-- Edit --}}
            <a href="{{ route('additional.edit', $additional->id) }}" class="btn btn-primary px-4 py-2">
                <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
            </a>

            {{-- Back --}}
            <a href="{{ route('additional.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
            </a>
        </div>
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">

        <div class="row g-4">

            {{-- Image --}}
            <div class="col-md-4 d-flex align-items-start">
                <div class="w-100">
                    <small class="text-muted d-block mb-1">
                        {{ __('adminlte::adminlte.image') }}
                    </small>

                    <div class="border rounded p-2 text-center bg-light">
                        @php
                            $image = $additional->image ?? null;
                        @endphp

                        @if ($image)
                            <img id="additional-image"
                                 src="{{ $image }}"
                                 alt="Additional image"
                                 class="img-fluid rounded"
                                 style="max-height: 220px; object-fit: contain;">
                        @else
                            <div id="additional-image-fallback"
                                 class="text-muted py-5">
                                <i class="far fa-image fa-3x d-block mb-2"></i>
                                {{ __('adminlte::adminlte.no_image') }}
                            </div>
                            <img id="additional-image"
                                 src=""
                                 alt=""
                                 class="img-fluid rounded d-none"
                                 style="max-height: 220px; object-fit: contain;">
                        @endif
                    </div>
                </div>
            </div>

            {{-- Text fields --}}
            <div class="col-md-8">
                <div class="row gy-3">

                    {{-- Name EN --}}
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                            {{ __('adminlte::adminlte.name_en') }}
                        </small>
                        <div id="additional-name-en" class="fs-5 fw-bold text-dark">
                            {{ $additional->name_en ?? '-' }}
                        </div>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                            {{ __('adminlte::adminlte.name_ar') }}
                        </small>
                        <div id="additional-name-ar"
                             class="fs-5 fw-bold text-dark"
                             @if(app()->isLocale('ar')) dir="rtl" @endif>
                            {{ $additional->name_ar ?? '-' }}
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                            {{ __('adminlte::adminlte.price') }}
                        </small>
                        <div id="additional-price" class="fs-5 fw-semibold text-dark">
                            {{ $additional->price ?? '-' }}
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                            {{ __('adminlte::adminlte.is_active') }}
                        </small>
                        <span id="additional-status"
                              class="badge {{ $additional->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                            @if($additional->is_active)
                                <i class="fas fa-check-circle me-1"></i>
                                {{ __('adminlte::adminlte.active') }}
                            @else
                                <i class="fas fa-times-circle me-1"></i>
                                {{ __('adminlte::adminlte.inactive') }}
                            @endif
                        </span>
                    </div>

                    {{-- Description --}}
                    <div class="col-12 mt-3">
                        <small class="text-muted d-block mb-1">
                            {{ __('adminlte::adminlte.descripation') }}
                        </small>
                        <p id="additional-description" class="mb-0 text-muted">
                            {{ $additional->description ?: '-' }}
                        </p>
                    </div>

                </div>
            </div>

        </div>

    </x-adminlte-card>
</div>

{{-- Broadcast listener anchor --}}
<div id="additional-listener"
     data-channel="{{ $broadcast['channel'] ?? 'additional' }}"
     data-events='@json($broadcast['events'] ?? ["additional_updated"])'
     data-additional-id="{{ $additional->id }}"
></div>
@endsection

@push('js')
@once
<script>
(function () {
  'use strict';

  function norm(v) {
    if (v === undefined || v === null) return '';
    return String(v);
  }

  function updateText(id, value, fallback = '-') {
    const el = document.getElementById(id);
    if (!el) return;
    const v = norm(value);
    el.textContent = v.trim() !== '' ? v : fallback;
  }

  function updateImage(url) {
    const img      = document.getElementById('additional-image');
    const fallback = document.getElementById('additional-image-fallback');

    if (!img) return;

    if (url && String(url).trim() !== '') {
      img.src = url;
      img.classList.remove('d-none');
      if (fallback) fallback.classList.add('d-none');
    } else {
      img.src = '';
      img.classList.add('d-none');
      if (fallback) fallback.classList.remove('d-none');
    }
  }

  function updateStatus(isActive) {
    const statusEl = document.getElementById('additional-status');
    if (!statusEl) return;

    const on = !!Number(isActive);

    statusEl.classList.remove('bg-success', 'bg-danger');
    statusEl.classList.add(on ? 'bg-success' : 'bg-danger');
    statusEl.innerHTML = on
      ? '<i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}'
      : '<i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}';
  }

  // Apply payload coming from broadcast → update UI
  function applyAdditionalPayload(payload) {
    if (!payload) return;

    const a = payload.additional ?? payload ?? {};

    // Optional: ignore if id doesn't match this row
    const anchor   = document.getElementById('additional-listener');
    const currentId = anchor ? anchor.dataset.additionalId : null;
    if (currentId && a.id && String(a.id) !== String(currentId)) {
      return;
    }

    updateText('additional-name-en', a.name_en);
    updateText('additional-name-ar', a.name_ar);
    updateText('additional-price',   a.price);
    updateText('additional-description', a.description);

    updateStatus(a.is_active);
    updateImage(a.image_url || a.image);

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch (_) {}
    }

    console.log('[additional show] updated from broadcast', a);
  }

  // Optional: expose globally if you want to call manually
  window.updateAdditionalShow = applyAdditionalPayload;

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('additional-listener');
    if (!anchor) {
      console.warn('[additional show] listener anchor not found');
      return;
    }

    const channel = anchor.dataset.channel || 'additional';
    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["additional_updated"]');
    } catch (_) {
      events = ['additional_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['additional_updated'];
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    const handler = function (e) {
      applyAdditionalPayload(e && (e.additional ?? e.payload ?? e));
    };

    // Register for layout-level broadcaster
    events.forEach(function (ev) {
      window.__pageBroadcasts.push({
        channel: channel,
        event:   ev,
        handler: handler,
      });

      // If AppBroadcast already exists, subscribe now
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channel, ev, handler);
        console.info('[additional show] subscribed via AppBroadcast →', channel, '/', ev);
      }
    });
  });

})();
</script>å
@endonce
@endpush
