@extends('adminlte::page')

@section('title', __('adminlte::adminlte.type'))

@section('content')
<div class="container py-4">
{{-- Header --}}
 <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-code-branch me-2 text-primary"></i>
            @if (app()->getLocale() === 'ar') {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.type') }}@else{{ __('adminlte::adminlte.type') }} {{ __('adminlte::adminlte.details') }}@endif
        </h2>
    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">



            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">
 {{-- Name in English --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $type->name_en }}</div>
                    </div>
                    {{-- Name in Arabic --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $type->name_ar }}</div>
                    </div>


                    {{-- Status --}}
                    <div class="col-12">
                        @if($type->is_active)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            </span>
                        @endif
                    </div>



                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('type.edit', $type->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('type.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
@endsection
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.type'))

@section('content')
<div class="container py-4">
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    {{-- Header --}}
    <h2 class="h4 mb-0 text-dark fw-bold">
        <i class="fas fa-code-branch me-2 text-primary"></i>
        @if ($isAr)
            {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.type') }}
        @else
            {{ __('adminlte::adminlte.type') }} {{ __('adminlte::adminlte.details') }}
        @endif
    </h2>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm mt-3">
        <div class="row g-4">
            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Name in English --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="type-name-en">{{ $type->name_en }}</div>
                    </div>

                    {{-- Name in Arabic --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="type-name-ar">{{ $type->name_ar }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($type->is_active)
                            <span id="type-status-badge" class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i>
                                <span id="type-status-text">{{ __('adminlte::adminlte.active') }}</span>
                            </span>
                        @else
                            <span id="type-status-badge" class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i>
                                <span id="type-status-text">{{ __('adminlte::adminlte.inactive') }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('type.edit', $type->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('type.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>

{{-- Listener anchor for window broadcasting --}}
<div id="type-show-listener"
     data-channel="types"
     data-events='["type_updated","TypeUpdated"]'
     data-type-id="{{ $type->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('type-show-listener');
    if (!anchor) {
      console.warn('[type-show] listener anchor not found');
      return;
    }

    const channelName = anchor.dataset.channel || 'types';

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["type_updated"]');
    } catch (_) {
      events = ['type_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['type_updated'];
    }

    const currentId = anchor.dataset.typeId || null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // Accept shapes: { payload: { type: {...} } }, { type: {...} }, or plain
        const raw = e?.payload || e?.type || e;
        const t   = raw?.type || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          // Not this type → ignore
          return;
        }

        // Optional DOM updates (even though we reload)
        if (t.name_en !== undefined) {
          const el = document.getElementById('type-name-en');
          if (el) el.textContent = String(t.name_en ?? '');
        }
        if (t.name_ar !== undefined) {
          const el = document.getElementById('type-name-ar');
          if (el) el.textContent = String(t.name_ar ?? '');
        }
        if (t.is_active !== undefined) {
          const badge = document.getElementById('type-status-badge');
          const text  = document.getElementById('type-status-text');
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

        // Reset page → reload to get latest data from server
        window.location.reload();
      };

      // Register for global broadcaster bootstrap
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // Subscribe immediately if AppBroadcast is already created
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[type-show] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[type-show] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
