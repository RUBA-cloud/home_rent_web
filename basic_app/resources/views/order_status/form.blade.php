{{-- resources/views/order_status/_form.blade.php --}}
@extends('adminlte::page')

@section('title')
    {{ strtoupper($method ?? 'POST') === 'POST'
        ? __('adminlte::adminlte.create').' '.__('adminlte::adminlte.order_status')
        : __('adminlte::adminlte.edit').' '.__('adminlte::adminlte.order_status') }}
@endsection

@php
    $statusObj  = $status ?? null;
    $httpMethod = strtoupper($method ?? 'POST');
@endphp

@section('content')
<div style="min-height: 100vh; display:flex;">
    <div class="card" style="padding:24px; width:100%;">
        <h2 style="font-size:2rem; font-weight:700; color:#22223B; margin-bottom:24px;">
            {{ $httpMethod === 'POST'
                ? __('adminlte::adminlte.create').' '.__('adminlte::adminlte.order_status')
                : __('adminlte::adminlte.edit').' '.__('adminlte::adminlte.order_status') }}
        </h2>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $action }}"
              id="order-status-form"
              enctype="multipart/form-data"
              data-channel="{{ $channel ?? 'order_status' }}"
              data-events='@json($events ?? ["order_status_updated"])'>
            @csrf
            @unless (in_array($httpMethod, ['GET','POST']))
                @method($httpMethod)
            @endunless

            {{-- Hidden ID if editing (optional) --}}
            @if(!empty($statusObj?->id))
                <input type="hidden" name="id" value="{{ $statusObj->id }}">
            @endif

            {{-- Name EN --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.name_en') }}"
                :value="old('name_en', data_get($statusObj, 'name_en', ''))"
            />
            @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

            {{-- Name AR --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.name_ar') }}"
                dir="rtl"
                :value="old('name_ar', data_get($statusObj, 'name_ar', ''))"
            />
            @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

            {{-- Is Active --}}
            <div class="form-group" style="margin:20px 0;">
                <input type="hidden" name="is_active" value="0">
                @php $isActive = old('is_active', (int) data_get($statusObj, 'is_active', 1)); @endphp
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ (int)$isActive ? 'checked' : '' }}>
                    {{ __('adminlte::adminlte.is_active') }}
                </label>
            </div>
            @error('is_active') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

            <x-adminlte-button
                label="{{ $httpMethod === 'POST'
                    ? __('adminlte::adminlte.save_information')
                    : __('adminlte::adminlte.update_information') }}"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
        </form>
    </div>
</div>
@endsection

@push('js')
@once
<script>
(function () {
  'use strict';

  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;

  function setField(form, name, value) {
    if (value === undefined || value === null) return;
    const els = form.querySelectorAll(`[name="${esc(name)}"]`);
    if (!els.length) return;

    els.forEach(el => {
      const type = (el.getAttribute('type') || el.tagName).toLowerCase();

      if (type === 'checkbox') {
        el.checked = !!Number(value);
        el.dispatchEvent(new Event('change', { bubbles: true }));
      } else if (type === 'radio') {
        el.checked = String(el.value) === String(value);
        el.dispatchEvent(new Event('change', { bubbles: true }));
      } else {
        el.value = String(value ?? '');
        el.dispatchEvent(new Event('input',  { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
  }

  function applyPayloadToForm(form, payload) {
    const data = payload?.order_status ?? payload?.status ?? payload ?? {};
    if (!data || typeof data !== 'object') return;

    setField(form, 'id',        data.id);
    setField(form, 'name_en',   data.name_en);
    setField(form, 'name_ar',   data.name_ar);
    setField(form, 'is_active', data.is_active);

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_) {}
    }

    form.classList.add('border', 'border-success');
    setTimeout(() => form.classList.remove('border', 'border-success'), 800);

    console.log('[order_status form] patched from broadcast:', data);
  }

  // Optional global helper
  window.updateOrderStatusForm = function (payload) {
    const form = document.getElementById('order-status-form');
    if (!form) return;
    applyPayloadToForm(form, payload);
  };

  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('order-status-form');
    if (!form) {
      console.warn('[order_status form] form not found');
      return;
    }

    const channelName = form.dataset.channel || 'order_status';

    let events;
    try {
      events = JSON.parse(form.dataset.events || '["order_status_updated"]');
    } catch (_) {
      events = ['order_status_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['order_status_updated'];
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    const handler = function (e) {
      // accept: {order_status:{...}}, {status:{...}}, or flat payload
      applyPayloadToForm(form, e && (e.order_status ?? e.status ?? e.payload ?? e));
    };

    // Register each event for layout-level subscription
    events.forEach(function (ev) {
      window.__pageBroadcasts.push({
        channel: channelName,  // must match broadcastOn()
        event:   ev,           // must match broadcastAs()
        handler: handler
      });

      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, ev, handler);
        console.info('[order_status form] subscribed via AppBroadcast →', channelName, '/', ev);
      }
    });

    if (!window.AppBroadcast) {
      console.info('[order_status form] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endonce
@endpush
