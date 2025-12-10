@extends('adminlte::page')

@section('title', __('adminlte::adminlte.payment'))

@section('content')
<div class="container py-4">

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div id="payment-name-en" class="fs-5 fw-bold text-dark">
                            {{ $payment->name_en }}
                        </div>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div id="payment-name-ar" class="fs-5 fw-bold text-dark">
                            {{ $payment->name_ar }}
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        <small class="text-muted d-block mb-1">{{ __('adminlte::adminlte.is_active') }}</small>
                        <span id="payment-status"
                              class="badge {{ $payment->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                            @if($payment->is_active)
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            @else
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            @endif
                        </span>
                    </div>

                    {{-- Address EN --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_en') }}</small>
                        <div id="payment-address-en" class="fw-semibold">
                            {{ $payment->address_en ?? '-' }}
                        </div>
                    </div>

                    {{-- Address AR --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_ar') }}</small>
                        <div id="payment-address-ar" class="fw-semibold">
                            {{ $payment->address_ar ?? '-' }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('payment.edit', $payment->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </x-adminlte-card>
</div>

{{-- Listener anchor for broadcasting --}}
<div id="payment-listener"
     data-channel="payments"
     data-events='["payment_updated","PaymentUpdated"]'
     data-payment-id="{{ $payment->id }}">
</div>
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

  // Apply payload → DOM
  function applyPaymentPayload(payload) {
    if (!payload) return;

    const p = payload.payment ?? payload ?? {};

    const anchor    = document.getElementById('payment-listener');
    const currentId = anchor ? anchor.dataset.paymentId : null;
    if (currentId && p.id && String(p.id) !== String(currentId)) {
      // event for another payment → ignore
      return;
    }

    // name_en
    const nameEnEl = document.getElementById('payment-name-en');
    if (nameEnEl && p.name_en !== undefined) {
      nameEnEl.textContent = norm(p.name_en) || '-';
    }

    // name_ar
    const nameArEl = document.getElementById('payment-name-ar');
    if (nameArEl && p.name_ar !== undefined) {
      nameArEl.textContent = norm(p.name_ar) || '-';
    }

    // address_en
    const addrEnEl = document.getElementById('payment-address-en');
    if (addrEnEl && p.address_en !== undefined) {
      addrEnEl.textContent = norm(p.address_en) || '-';
    }

    // address_ar
    const addrArEl = document.getElementById('payment-address-ar');
    if (addrArEl && p.address_ar !== undefined) {
      addrArEl.textContent = norm(p.address_ar) || '-';
    }

    // status
    const statusEl = document.getElementById('payment-status');
    if (statusEl && p.is_active !== undefined && p.is_active !== null) {
      const on = !!Number(p.is_active);
      statusEl.classList.remove('bg-success','bg-danger');
      statusEl.classList.add(on ? 'bg-success' : 'bg-danger');
      statusEl.innerHTML = on
        ? '<i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}'
        : '<i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}';
    }

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch (_) {}
    }

    console.log('[payments show] updated from broadcast', p);
  }

  // Optional global helper
  window.updatePaymentShow = applyPaymentPayload;

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('payment-listener');
    if (!anchor) {
      console.warn('[payments show] listener anchor not found');
      return;
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["payment_updated"]');
    } catch (_) {
      events = ['payment_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['payment_updated'];
    }

    const channelName = anchor.dataset.channel || 'payments';

    const handler = function (e) {
      // Try shapes: {payment:{...}} or {payload:{...}} or flat
      applyPaymentPayload(e && (e.payment ?? e.payload ?? e));
    };

    // Register for layout-level broadcaster
    events.forEach(function (ev) {
      window.__pageBroadcasts.push({
        channel: channelName,  // must match broadcastOn()
        event:   ev,           // must match broadcastAs()
        handler: handler
      });

      // If AppBroadcast is already available, subscribe now
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, ev, handler);
        console.info('[payments show] subscribed via AppBroadcast →', channelName, '/', ev);
      }
    });

    if (!window.AppBroadcast) {
      console.info('[payments show] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endonce
@endpush
