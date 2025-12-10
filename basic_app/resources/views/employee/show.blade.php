@extends('adminlte::page')

@section('title', __('adminlte::adminlte.employee_module'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        {{ __('adminlte::adminlte.employee') }} #{{ $employee->id }}
    </h1>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>{{ __('adminlte::adminlte.go_back') }}
    </a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @php
            // Determine which image to show
            $avatar = null;

            if (!empty($employee->avatar_url)) {
                $avatar = $employee->avatar_url;
            } elseif (!empty($employee->image)) {
                $avatar = asset('storage/' . $employee->image);
            } else {
                $avatar = asset('images/logo_image.png');
            }
        @endphp

        <div class="d-flex gap-3 align-items-center mb-3" style="padding: 5px">
            <img src="{{ $avatar }}" alt="avatar"
                 id="employee-avatar"
                 class="rounded-circle border"
                 style="width:70px;height:70px;object-fit:cover;margin:5px;"
                 data-placeholder="{{ $avatar }}">
            <div>
                <div id="employee-name" class="h5 mb-1">{{ $employee->name }}</div>
                <div id="employee-email" class="text-muted">{{ $employee->email }}</div>
            </div>
        </div>

        <h6 class="fw-bold">{{ __('adminlte::adminlte.permissions') }}</h6>

        <div id="employee-permissions">
            @if($employee->permissions->isEmpty())
                <div class="text-muted">{{ __('adminlte::menu.permissions') }}</div>
            @else
                <ul class="mb-0">
                    @foreach($employee->permissions as $p)
                        <li>{{ $p->display_name ?? ($p->name_en ?: $p->name_ar) }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

{{-- Listener anchor for broadcasting --}}
<div id="employee-listener"
     data-channel="employees"
     data-events='["employee_updated","EmployeeUpdated"]'
     data-employee-id="{{ $employee->id }}">
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

    function renderPermissions(container, permissions) {
        if (!container) return;

        if (!Array.isArray(permissions) || permissions.length === 0) {
            container.innerHTML = '<div class="text-muted">{{ __('adminlte::menu.permissions') }}</div>';
            return;
        }

        const ul = document.createElement('ul');
        ul.className = 'mb-0';

        permissions.forEach(p => {
            const li = document.createElement('li');
            const display = p.display_name || p.name_en || p.name_ar || p.name || '';
            li.textContent = display;
            ul.appendChild(li);
        });

        container.innerHTML = '';
        container.appendChild(ul);
    }

    function updateDomFromPayload(payload) {
        if (!payload) return;

        const e = payload.employee ?? payload ?? {};

        // Ensure it's the same employee
        const anchor = document.getElementById('employee-listener');
        const currentId = anchor ? anchor.dataset.employeeId : null;
        if (currentId && e.id && String(e.id) !== String(currentId)) {
            return; // different employee, ignore
        }

        // Name
        const nameEl = document.getElementById('employee-name');
        if (nameEl) nameEl.textContent = norm(e.name);

        // Email
        const emailEl = document.getElementById('employee-email');
        if (emailEl) emailEl.textContent = norm(e.email);

        // Avatar
        const avatarEl = document.getElementById('employee-avatar');
        if (avatarEl) {
            const newSrc = e.avatar_url || e.image_url || e.image || avatarEl.dataset.placeholder;
            if (newSrc) avatarEl.src = newSrc;
        }

        // Permissions
        const permsContainer = document.getElementById('employee-permissions');
        if (permsContainer && (Array.isArray(e.permissions) || e.permissions === null || e.permissions === undefined)) {
            renderPermissions(permsContainer, e.permissions || []);
        }

        if (window.toastr) {
            toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }

        console.log('[employee show] updated from broadcast payload', e);
    }

    // Optional: expose globally
    window.updateEmployeeShow = updateDomFromPayload;

    document.addEventListener('DOMContentLoaded', function () {
        const anchor = document.getElementById('employee-listener');
        if (!anchor) {
            console.warn('[employee show] listener anchor not found');
            return;
        }

        window.__pageBroadcasts = window.__pageBroadcasts || [];

        let events;
        try {
            events = JSON.parse(anchor.dataset.events || '["employee_updated"]');
        } catch (_) {
            events = ['employee_updated'];
        }
        if (!Array.isArray(events) || !events.length) {
            events = ['employee_updated'];
        }

        const handler = function (eventPayload) {
            updateDomFromPayload(eventPayload && (eventPayload.employee ?? eventPayload));
        };

        window.__pageBroadcasts.push({
            channel: 'employees',         // broadcastOn()
            event:   'employee_updated',  // broadcastAs()
            handler: handler
        });

        if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
            window.AppBroadcast.subscribe('employees', 'employee_updated', handler);
            console.info('[employee show] subscribed via AppBroadcast → employees / employee_updated');
        } else {
            console.info('[employee show] registered in __pageBroadcasts; layout will subscribe later.');
        }
    });
})();
</script>
@endpush
