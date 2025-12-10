@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_delivery'))

@section('content')
<div class="container py-4">

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="col-lg-8 col-md-7">
            <div class="row gy-3">

                {{-- Name EN --}}
                <div class="col-12">
                    <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                    <div id="company-delivery-name-en" class="fs-5 fw-bold text-dark">
                        {{ $company_delivery->name_en }}
                    </div>
                </div>

                {{-- Name AR --}}
                <div class="col-12">
                    <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                    <div id="company-delivery-name-ar" class="fs-5 fw-bold text-dark">
                        {{ $company_delivery->name_ar }}
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-12">
                    <span id="company-delivery-status"
                          class="badge {{ $company_delivery->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                        @if($company_delivery->is_active)
                            <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                        @else
                            <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                        @endif
                    </span>
                </div>

                {{-- Actions --}}
                <div class="col-12 pt-3">
                    <a href="{{ route('company_delivery.edit', $company_delivery->id) }}"
                       class="btn btn-primary px-4 py-2">
                        <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                    </a>
                    <a href="{{ route('company_delivery.index') }}"
                       class="btn btn-outline-secondary ms-2 px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                    </a>
                </div>

            </div>
        </div>
    </x-adminlte-card>
</div>

{{-- 🔔 Listener anchor (used by JS to know which record & channel) --}}
<div id="company-delivery-listener"
     data-channel="company_delivery"
     data-events='["company_delivery_updated","CompanyDeliveryUpdated"]'
     data-delivery-id="{{ $company_delivery->id }}">
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

    // Update the visible text / status from broadcast payload
    function updateDomFromPayload(payload) {
        if (!payload) return;

        const d = payload.delivery ?? payload ?? {};

        const anchor = document.getElementById('company-delivery-listener');
        const currentId = anchor ? anchor.dataset.deliveryId : null;
        if (currentId && d.id && String(d.id) !== String(currentId)) {
            // event for another delivery record – ignore
            return;
        }

        // Name EN
        const nameEnEl = document.getElementById('company-delivery-name-en');
        if (nameEnEl) {
            nameEnEl.textContent = norm(d.name_en);
        }

        // Name AR
        const nameArEl = document.getElementById('company-delivery-name-ar');
        if (nameArEl) {
            nameArEl.textContent = norm(d.name_ar);
        }

        // Status badge
        const statusEl = document.getElementById('company-delivery-status');
        if (statusEl && d.is_active !== undefined && d.is_active !== null) {
            const isOn = Number(d.is_active) === 1;
            statusEl.classList.remove('bg-success', 'bg-danger');
            statusEl.classList.add(isOn ? 'bg-success' : 'bg-danger');
            statusEl.innerHTML = isOn
                ? '<i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}'
                : '<i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}';
        }

        if (window.toastr) {
            toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }

        console.log('[company_delivery show] updated from broadcast payload', d);
    }

    // Optional global hook
    window.updateCompanyDeliveryShow = updateDomFromPayload;

    document.addEventListener('DOMContentLoaded', function () {
        const anchor = document.getElementById('company-delivery-listener');
        if (!anchor) {
            console.warn('[company_delivery show] listener anchor not found');
            return;
        }

        // Register with global broadcasting (same style as additional/category/branch)
        window.__pageBroadcasts = window.__pageBroadcasts || [];

        const handler = function (e) {
            updateDomFromPayload(e && (e.delivery ?? e));
        };

        window.__pageBroadcasts.push({
            channel: 'company_delivery',           // broadcastOn()
            event:   'company_delivery_updated',   // broadcastAs()
            handler: handler
        });

        if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
            window.AppBroadcast.subscribe('company_delivery', 'company_delivery_updated', handler);
            console.info('[company_delivery show] subscribed via AppBroadcast → company_delivery / company_delivery_updated');
        } else {
            console.info('[company_delivery show] registered in __pageBroadcasts; layout will subscribe later.');
        }
    });
})();
</script>
@endpush
