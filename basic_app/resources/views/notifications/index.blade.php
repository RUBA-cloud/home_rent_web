@extends('adminlte::page')

@section('title', __('Notifications'))

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
  <h1 class="m-0">{{ __('adminlte::adminlte.Notifications') }}</h1>
  <form action="{{ route('notifications.markAll') }}" method="POST" class="m-0">
    @csrf
    <button class="btn btn-outline-secondary btn-sm">{{ __('adminlte::adminlte.mark_all_as_read') }}</button>
  </form>
</div>
@stop

@section('content')
<div class="card">
  <div class="card-body">

    <form class="mb-3" method="GET" style="margin: 5px; padding: 5px;">
      <div class="row g-2">
        <div class="col-auto">
          <select name="filter" class="form-control" onchange="this.form.submit()">
            <option value="all"    @selected(request('filter')==='all')>{{ __('adminlte::adminlte.all') }}</option>
            <option value="unread" @selected(request('filter')==='unread')>{{ __('adminlte::adminlte.unread') }}</option>
          </select>
        </div>
      </div>
    </form>

    <div id="notifList" class="list-group">
      @forelse($items as $n)
        <div class="list-group-item d-flex align-items-start {{ is_null($n->read_at) ? 'bg-light' : '' }}"
             style="margin:6px;border-radius:10px;box-shadow:0 2px 4px rgba(0,0,0,.08)">
          <i class="{{ $n->icon ?: 'fas fa-bell' }} me-3 mt-1"></i>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <strong>{{ $n->title }}</strong>
              <small class="text-muted">{{ $n->created_at->format('Y-m-d H:i') }}</small>
            </div>
            @if($n->body)
              <div class="text-muted">{{ $n->body }}</div>
            @endif
            <div class="mt-2 d-flex flex-wrap gap-2">
              @if($n->link)
                <a href="{{ $n->link }}" class="btn btn-sm btn-primary" style="margin: 5px">{{ __('adminlte::adminlte.open') }}</a>
              @endif
              @if(is_null($n->read_at))
                <form action="{{ route('notifications.mark', $n) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-secondary" style="margin: 5px">{{ __('adminlte::adminlte.mark_as_read') }}</button>
                </form>
              @endif
              <form action="{{ route('notifications.destroy', $n) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('{{ __('Are you sure?') }}');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" style="margin: 5px">{{__('adminlte::adminlte.delete') }}</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="text-center text-muted py-4">{{ __('adminlte::adminlte.Notifications') }}</div>
      @endforelse
    </div>

    <div class="mt-3">
      {{ $items->links() }}
    </div>
  </div>
</div>

{{-- Listener anchor --}}
<div id="notifications-listener"
     data-channel="notifications"
     data-events='["notification.created","NotificationCreated"]'
     data-user-id="{{ auth()->id() }}">
</div>
@stop

@push('js')
<script>
(function () {
  'use strict';

  const list = document.getElementById('notifList');
  if (!list) return;

  const esc  = s => String(s ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');
  const fmt  = dt => {
    try { const d=new Date(dt); const pad=n=>String(n).padStart(2,'0');
      return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    } catch { return ''; }
  };

  function makeItem(n) {
    const wrap = document.createElement('div');
    wrap.className = 'list-group-item d-flex align-items-start bg-light';
    Object.assign(wrap.style,{margin:'6px',borderRadius:'10px',boxShadow:'0 2px 4px rgba(0,0,0,.08)'});

    wrap.innerHTML = `
      <i class="${esc(n.icon || 'fas fa-bell')} me-3 mt-1"></i>
      <div class="flex-grow-1">
        <div class="d-flex justify-content-between">
          <strong>${esc(n.title || '{{ __("Notification") }}')}</strong>
          <small class="text-muted">${fmt(n.created_at)}</small>
        </div>
        ${n.body ? `<div class="text-muted">${esc(n.body)}</div>` : ''}
        <div class="mt-2 d-flex flex-wrap gap-2">
          ${n.link ? `<a href="${esc(n.link)}" class="btn btn-sm btn-primary">{{ __('Open') }}</a>` : ''}
        </div>
      </div>
    `;
    return wrap;
  }

  function appendNotification(payload) {
    if (!payload) return;
    const n = payload.notification ?? payload ?? {};
    const el = makeItem(n);
    list.insertBefore(el, list.firstChild);
    if (window.toastr) toastr.info(n.title || '{{ __("New Notification") }}');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const anchor = document.getElementById('notifications-listener');
    if (!anchor) return;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    const handler = function (e) {
      appendNotification(e && (e.notification ?? e));
    };

    window.__pageBroadcasts.push({
      channel: 'notifications',         // broadcastOn()
      event:   'notification.created',  // broadcastAs()
      handler: handler
    });

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe('notifications', 'notification.created', handler);
      console.info('[notifications] listening via AppBroadcast → notifications / notification.created');
    } else {
      console.info('[notifications] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endpush
