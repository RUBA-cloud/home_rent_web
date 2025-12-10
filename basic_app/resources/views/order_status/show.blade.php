{{-- resources/views/order_status/show.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.order_status'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">

        <x-adminlte-card title="{{ __('adminlte::adminlte.order_status') }}"
                         theme="info"
                         icon="fas fa-info-circle"
                         collapsible>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.name_en')}}:</strong>
                    <div id="order-status-name-en"
                         class="form-control-plaintext">
                        {{ $orderStatus->name_en ?? '-' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.name_ar')}}:</strong>
                    <div id="order-status-name-ar"
                         class="form-control-plaintext">
                        {{ $orderStatus->name_ar ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.status') }}:</strong><br>
                <span id="order-status-active"
                      class="badge {{ $orderStatus->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $orderStatus->is_active
                        ? __('adminlte::adminlte.active')
                        : __('adminlte::adminlte.inactive') }}
                </span>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('order_status.edit', $orderStatus->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> {{ __('adminlte::adminlte.edit') }}
                </a>
            </div>

        </x-adminlte-card>

    </div>
</div>

{{-- Listener anchor for broadcasting --}}
<div id="order-status-listener"
     data-channel="order_status"
     data-events='["order_status_updated","OrderStatusUpdated"]'
     data-order-status-id="{{ $orderStatus->id }}">
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

  function updateDomFromPayload(payload) {
    if (!payload) return;

    // Support different shapes: { orderStatus: {...} } or { order_status: {...} } or plain
    const t = payload.orderStatus ?? payload.order_status ?? payload ?? {};

    // Only update if this is the same status
    const anchor    = document.getElementById('order-status-listener');
    const currentId = anchor ? anchor.dataset.orderStatusId : null;
    if (currentId && t.id && String(t.id) !== String(currentId)) {
      return;
    }

    // Name EN
    const nameEnEl = document.getElementById('order-status-name-en');
    if (nameEnEl) nameEnEl.textContent = norm(t.name_en) || '-';

    // Name AR
    const nameArEl = document.getElementById('order-status-name-ar');
    if (nameArEl) nameArEl.textContent = norm(t.name_ar) || '-';

    // Active badge
    if (t.is_active !== undefined) {
      const el = document.getElementById('order-status-active');
      if (el) {
        const on = !!Number(t.is_active);
        el.classList.remove('bg-success', 'bg-secondary', 'bg-danger');
        el.classList.add(on ? 'bg-success' : 'bg-secondary');
        el.textContent = on
          ? '{{ __("adminlte::adminlte.active") }}'
          : '{{ __("adminlte::adminlte.inactive") }}';
      }
    }

    if (window.toastr) {
      toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
    }

    console.log('[order_status show] updated from broadcast payload', t);
  }

  // Optional global helper if you want to trigger manually
  window.updateOrderStatusShow = updateDomFromPayload;

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('order-status-listener');
    if (!anchor) {
      console.warn('[order_status show] listener anchor not found');
      return;
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["order_status_updated"]');
    } catch (_) {
      events = ['order_status_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['order_status_updated'];
    }

    const handler = function (e) {
      updateDomFromPayload(e && (e.orderStatus ?? e.order_status ?? e));
    };

    // Register for your global broadcast bootstrapper
    window.__pageBroadcasts.push({
      channel: 'order_status',        // broadcastOn()
      event:   'order_status_updated', // broadcastAs()
      handler: handler
    });

    // If you already have window.AppBroadcast ready, subscribe now
    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe('order_status', 'order_status_updated', handler);
      console.info('[order_status show] subscribed via AppBroadcast → order_status / order_status_updated');
    } else {
      console.info('[order_status show] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endpush
