{{-- resources/views/permissions/show.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.permissions'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.permissions') }} #{{ $permission->id }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">{{ __('adminlte::adminlte.permissions') }}</dt>
                <dd class="col-sm-9" id="perm-module-name">
                    {{ $permission->module_name }}
                </dd>

                <dt class="col-sm-3">{{ __('adminlte::adminlte.name_en') }}</dt>
                <dd class="col-sm-9" id="perm-name-en">
                    {{ $permission->name_en }}
                </dd>

                <dt class="col-sm-3">{{ __('adminlte::adminlte.name_ar') }}</dt>
                <dd class="col-sm-9" id="perm-name-ar">
                    {{ $permission->name_ar }}
                </dd>

                <dt class="col-sm-3">{{ __('adminlte::adminlte.name_en') }}</dt>
                <dd class="col-sm-9">
                    <span id="perm-can-edit"
                          class="badge {{ $permission->can_edit ? 'badge-success' : 'badge-secondary' }}">
                        {{ __('adminlte::adminlte.edit') }}
                    </span>
                    <span id="perm-can-delete"
                          class="badge {{ $permission->can_delete ? 'badge-success' : 'badge-secondary' }}">
                        {{ __('adminlte::adminlte.delete') }}
                    </span>
                    <span id="perm-can-add"
                          class="badge {{ $permission->can_add ? 'badge-success' : 'badge-secondary' }}">
                        {{ __('adminlte::adminlte.add') }}
                    </span>
                    <span id="perm-can-view-history"
                          class="badge {{ $permission->can_view_history ? 'badge-success' : 'badge-secondary' }}">
                        {{ __('adminlte::adminlte.view_history') }}
                    </span>
                </dd>

                <dt class="col-sm-3">{{ __('adminlte::adminlte.active') }}</dt>
                <dd class="col-sm-9">
                    @if($permission->is_active)
                        <span id="perm-active" class="badge badge-success">{{ __('adminlte::adminlte.yes') }}</span>
                    @else
                        <span id="perm-active" class="badge badge-secondary">{{ __('adminlte::adminlte.no') }}</span>
                    @endif
                </dd>
            </dl>

            <div class="d-flex justify-content-end mt-4">
                <div class="col-12 pt-3 text-end">
                    <a href="{{ route('permissions.edit', $permission->id) }}"
                       class="btn btn-primary px-4 py-2">
                        <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                    </a>
                    <a href="{{ route('permissions.index') }}"
                       class="btn btn-outline-secondary ms-2 px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Listener anchor for window/AppBroadcast --}}
<div id="permission-show-listener"
     data-channel="permissions"
     data-events='["permissions_updated","PermissionUpdated"]'
     data-permission-id="{{ $permission->id }}">
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

  // Optional: update DOM before reload (in case you later decide not to reload)
  function updateDomFromPayload(t) {
    const moduleEl = document.getElementById('perm-module-name');
    if (moduleEl && t.module_name !== undefined) {
      moduleEl.textContent = norm(t.module_name);
    }

    const nameEnEl = document.getElementById('perm-name-en');
    if (nameEnEl && t.name_en !== undefined) {
      nameEnEl.textContent = norm(t.name_en);
    }

    const nameArEl = document.getElementById('perm-name-ar');
    if (nameArEl && t.name_ar !== undefined) {
      nameArEl.textContent = norm(t.name_ar);
    }

    // badges
    const canEditEl = document.getElementById('perm-can-edit');
    if (canEditEl && t.can_edit !== undefined) {
      const on = !!Number(t.can_edit);
      canEditEl.classList.remove('badge-success', 'badge-secondary');
      canEditEl.classList.add(on ? 'badge-success' : 'badge-secondary');
    }

    const canDeleteEl = document.getElementById('perm-can-delete');
    if (canDeleteEl && t.can_delete !== undefined) {
      const on = !!Number(t.can_delete);
      canDeleteEl.classList.remove('badge-success', 'badge-secondary');
      canDeleteEl.classList.add(on ? 'badge-success' : 'badge-secondary');
    }

    const canAddEl = document.getElementById('perm-can-add');
    if (canAddEl && t.can_add !== undefined) {
      const on = !!Number(t.can_add);
      canAddEl.classList.remove('badge-success', 'badge-secondary');
      canAddEl.classList.add(on ? 'badge-success' : 'badge-secondary');
    }

    const canHistEl = document.getElementById('perm-can-view-history');
    if (canHistEl && t.can_view_history !== undefined) {
      const on = !!Number(t.can_view_history);
      canHistEl.classList.remove('badge-success', 'badge-secondary');
      canHistEl.classList.add(on ? 'badge-success' : 'badge-secondary');
    }

    const activeEl = document.getElementById('perm-active');
    if (activeEl && t.is_active !== undefined) {
      const on = !!Number(t.is_active);
      activeEl.classList.remove('badge-success', 'badge-secondary');
      activeEl.classList.add(on ? 'badge-success' : 'badge-secondary');
      activeEl.textContent = on
        ? '{{ __("adminlte::adminlte.yes") }}'
        : '{{ __("adminlte::adminlte.no") }}';
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('permission-show-listener');
    if (!anchor) {
      console.warn('[permissions-show] listener anchor not found');
      return;
    }

    const channelName = anchor.dataset.channel || 'permissions';

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["permissions_updated"]');
    } catch (_) {
      events = ['permissions_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['permissions_updated'];
    }

    const currentId = anchor.dataset.permissionId || null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // Try different shapes: { payload: {...} }, { permission: {...} }, or plain
        const raw = e?.payload || e?.permission || e;
        const t = raw?.permission || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          return; // ignore other permissions
        }

        // Update DOM (optional)
        updateDomFromPayload(t);

        if (window.toastr) {
          toastr.info(@json(__('adminlte::adminlte.saved_successfully')));
        }

        // Reset page: reload to get fully fresh state from server
        window.location.reload();
      };

      // Register so your global JS can subscribe later if needed
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // If AppBroadcast is already available, subscribe now
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[permissions-show] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[permissions-show] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
