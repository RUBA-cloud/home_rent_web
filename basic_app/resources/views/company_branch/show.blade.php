@extends('adminlte::page')

@section('title', __('adminlte::adminlte.branch_details'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-code-branch me-2 text-primary"></i>
            {{ __('adminlte::adminlte.branch_details') }}
        </h2>

        {{-- Main branch badge (can be toggled by broadcast) --}}
        <span id="branch-main-badge"
              class="badge bg-purple text-white px-3 py-2 {{ $branch->is_main_branch ? '' : 'd-none' }}">
            <i class="fas fa-star me-1"></i>
            {{ __('adminlte::adminlte.main_branch') }}
        </span>
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">

            {{-- Image --}}
            <div class="col-lg-4 col-md-5">
                <div class="border rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center p-2 h-100">
                    @php
                        $imgSrc = $branch->image
                            ? asset($branch->image)
                            : 'https://placehold.co/500x300?text=Branch+Image';
                    @endphp
                    <img
                        id="branch-image"
                        src="{{ $imgSrc }}"
                        alt="Branch Image"
                        class="img-fluid rounded-3"
                        style="max-height: 280px; object-fit: cover;"
                        data-placeholder="{{ $imgSrc }}"
                    >
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Branch Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.branch_name_en') }}</small>
                        <div id="branch-name-en" class="fs-5 fw-bold text-dark">
                            {{ $branch->name_en }}
                        </div>
                    </div>

                    {{-- Branch Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.branch_name_ar') }}</small>
                        <div id="branch-name-ar" class="fs-5 fw-bold text-dark">
                            {{ $branch->name_ar }}
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        <span id="branch-status"
                              class="badge {{ $branch->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                            @if($branch->is_active)
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            @else
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            @endif
                        </span>
                    </div>

                    {{-- Contact Info --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_phone') }}</small>
                        <div id="branch-phone" class="fw-semibold">{{ $branch->phone ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_email') }}</small>
                        <div id="branch-email" class="fw-semibold">{{ $branch->email ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.fax') }}</small>
                        <div id="branch-fax" class="fw-semibold">{{ $branch->fax ?? '-' }}</div>
                    </div>

                    {{-- Addresses --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_en') }}</small>
                        <div id="branch-address-en" class="fw-semibold">{{ $branch->address_en ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_ar') }}</small>
                        <div id="branch-address-ar" class="fw-semibold">{{ $branch->address_ar ?? '-' }}</div>
                    </div>

                    {{-- Location --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.location') }}</small>
                        <div id="branch-location">
                            @if($branch->location)
                                <a id="branch-location-link" href="{{ $branch->location }}" target="_blank" class="text-primary fw-semibold">
                                    <i class="fas fa-map-marker-alt me-1"></i> {{ __('adminlte::adminlte.view_on_map') }}
                                </a>
                            @else
                                <span id="branch-location-empty">-</span>
                            @endif
                        </div>
                    </div>

                    {{-- Working Days / Hours --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.working_days') }}</small>
                        <div id="branch-working-days" class="fw-semibold">
                            @php
                                $days = $branch->working_days ? explode(',', $branch->working_days) : [];
                                $days = array_map('trim', $days);
                            @endphp
                            {{ $days ? implode(', ', $days) : '-' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.working_time') }}</small>
                        <div id="branch-working-time" class="fw-semibold">
                            {{ $branch->working_hours_from ?? '-' }} - {{ $branch->working_hours_to ?? '-' }}
                        </div>
                    </div>

                    {{-- Company Info --}}
                    @if ($branch->companyInfo)
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('adminlte::adminlte.company_name_en') }}</small>
                            <div id="branch-company-name-en" class="fw-semibold">{{ $branch->companyInfo->name_en ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('adminlte::adminlte.company_name_ar') }}</small>
                            <div id="branch-company-name-ar" class="fw-semibold">{{ $branch->companyInfo->name_ar ?? '-' }}</div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('adminlte::adminlte.company_name_en') }}</small>
                            <div id="branch-company-name-en" class="fw-semibold">-</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('adminlte::adminlte.company_name_ar') }}</small>
                            <div id="branch-company-name-ar" class="fw-semibold">-</div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('companyBranch.edit', $branch->id) }}"
                           class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('companyBranch.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>

{{-- Listener anchor --}}
<div id="branch-listener"
     data-channel="company_branch"
     data-events='["company_branch_updated","CompanyBranchUpdated"]'
     data-branch-id="{{ $branch->id }}">
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

    function getBranchFromPayload(payload) {
        if (!payload) return {};

        if (payload.branch) {
            return payload.branch;
        }
        if (payload.data && payload.data.branch) {
            return payload.data.branch;
        }

        // Also accept { company: {...} } if you ever send that
        if (payload.company) {
            return payload.company;
        }
        if (payload.data && payload.data.company) {
            return payload.data.company;
        }

        return payload;
    }

    function updateDomFromPayload(payload) {
        if (!payload) return;

        const b = getBranchFromPayload(payload) || {};

        const anchor = document.getElementById('branch-listener');
        if (!anchor) return;

        const currentId = anchor.dataset.branchId;
        if (currentId && b.id && String(b.id) !== String(currentId)) {
            return; // other branch
        }

        console.log('[branch show] applying payload', b);

        // Names
        const nameEnEl = document.getElementById('branch-name-en');
        if (nameEnEl) nameEnEl.textContent = norm(b.name_en);

        const nameArEl = document.getElementById('branch-name-ar');
        if (nameArEl) nameArEl.textContent = norm(b.name_ar);

        // Status
        const statusEl = document.getElementById('branch-status');
        if (statusEl && b.is_active !== undefined && b.is_active !== null) {
            const isOn = Number(b.is_active) === 1;
            statusEl.classList.remove('bg-success', 'bg-danger');
            statusEl.classList.add(isOn ? 'bg-success' : 'bg-danger');
            statusEl.innerHTML = isOn
                ? '<i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}'
                : '<i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}';
        }

        // Main branch badge
        const mainBadge = document.getElementById('branch-main-badge');
        if (mainBadge && b.is_main_branch !== undefined && b.is_main_branch !== null) {
            const isMain = Number(b.is_main_branch) === 1;
            isMain ? mainBadge.classList.remove('d-none') : mainBadge.classList.add('d-none');
        }

        // Contact info
        const phoneEl = document.getElementById('branch-phone');
        if (phoneEl) phoneEl.textContent = norm(b.phone) || '-';

        const emailEl = document.getElementById('branch-email');
        if (emailEl) emailEl.textContent = norm(b.email) || '-';

        const faxEl = document.getElementById('branch-fax');
        if (faxEl) faxEl.textContent = norm(b.fax) || '-';

        // Addresses
        const addrEnEl = document.getElementById('branch-address-en');
        if (addrEnEl) addrEnEl.textContent = norm(b.address_en) || '-';

        const addrArEl = document.getElementById('branch-address-ar');
        if (addrArEl) addrArEl.textContent = norm(b.address_ar) || '-';

        // Location
        const locationWrapper = document.getElementById('branch-location');
        if (locationWrapper) {
            const href = norm(b.location);
            if (href) {
                locationWrapper.innerHTML =
                    '<a id="branch-location-link" href="' + href + '" target="_blank" class="text-primary fw-semibold">' +
                        '<i class="fas fa-map-marker-alt me-1"></i> {{ __('adminlte::adminlte.view_on_map') }}' +
                    '</a>';
            } else {
                locationWrapper.innerHTML = '<span id="branch-location-empty">-</span>';
            }
        }

        // Working days
        const daysEl = document.getElementById('branch-working-days');
        if (daysEl) {
            let daysText = '-';
            if (Array.isArray(b.working_days)) {
                daysText = b.working_days.length ? b.working_days.join(', ') : '-';
            } else if (typeof b.working_days === 'string') {
                const parts = b.working_days.split(',').map(s => s.trim()).filter(Boolean);
                daysText = parts.length ? parts.join(', ') : '-';
            }
            daysEl.textContent = daysText;
        }

        // Working time
        const timeEl = document.getElementById('branch-working-time');
        if (timeEl) {
            const from = norm(b.working_hours_from || (b.working_hours?.from ?? ''));
            const to   = norm(b.working_hours_to   || (b.working_hours?.to   ?? ''));
            timeEl.textContent = (from || '-') + ' - ' + (to || '-');
        }

        // Company info
        const compEnEl = document.getElementById('branch-company-name-en');
        const compArEl = document.getElementById('branch-company-name-ar');
        const company  = b.companyInfo || b.company_info || {};
        if (compEnEl) compEnEl.textContent = norm(company.name_en) || '-';
        if (compArEl) compArEl.textContent = norm(company.name_ar) || '-';

        // Image
        const imgEl = document.getElementById('branch-image');
        if (imgEl) {
            const newSrc = b.image_url || b.image || imgEl.dataset.placeholder;
            if (newSrc) imgEl.src = newSrc;
        }

        if (window.toastr) {
            toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }
    }

    window.updateBranchShow = updateDomFromPayload;

    function attachSubscriptions() {
        const anchor = document.getElementById('branch-listener');
        if (!anchor) {
            console.warn('[branch show] listener anchor not found');
            return;
        }

        const channelName = anchor.dataset.channel || 'company_branch';

        let events = ['company_branch_updated'];
        const rawEvents = anchor.dataset.events;
        if (rawEvents) {
            try {
                const parsed = JSON.parse(rawEvents);
                if (Array.isArray(parsed) && parsed.length) {
                    events = parsed;
                }
            } catch (e) {
                console.warn('[branch show] failed to parse data-events, using default', e);
            }
        }

        const handler = function (e) {
            console.log('[branch show] received event', e);
            updateDomFromPayload(e);
        };

        if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
            events.forEach(function (ev) {
                window.AppBroadcast.subscribe(channelName, ev, handler);
                console.info('[branch show] listening via AppBroadcast →', channelName, '/', ev);
            });
            return;
        }

        if (window.pusher) {
            const ch = window.pusher.subscribe(channelName);
            events.forEach(function (ev) {
                ch.bind(ev, handler);
                console.info('[branch show] listening via window.pusher →', channelName, '/', ev);
            });
            return;
        }

        console.warn('[branch show] No AppBroadcast or window.pusher found – cannot subscribe to Pusher.');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attachSubscriptions);
    } else {
        attachSubscriptions();
    }
})();
</script>
@endpush
