@extends('adminlte::page')

@section('title', __('adminlte::adminlte.product'))

@section('content')
<div class="container-fluid">
    <x-adminlte-card title="{{ $product->name_en }} ({{ $product->name_ar }})"
                     theme="light"
                     theme-mode="outline"
                     icon="fas fa-box"
                     class="shadow rounded-3">

        {{-- Status & Price --}}
        <div class="mb-3">
            <x-adminlte-badge
                label="{{ $product->is_active ? __('adminlte::adminlte.is_active') : __('adminlte::adminlte.is_not_active') }}"
                theme="{{ $product->is_active ? 'success' : 'danger' }}"
            />
            <x-adminlte-badge
                label="Price: {{ number_format($product->price, 2) }} JD"
                theme="secondary"
                class="ms-2"
            />
        </div>

        {{-- Type & Category --}}
        <div class="mb-3">
            <strong>{{ __('adminlte::adminlte.type') }}:</strong>
            @if(app()->getLocale()=="ar")
                {{ optional($product->type)->name_ar ?? '-' }}
            @else
                {{ optional($product->type)->name_en ?? '-' }}
            @endif
            <br>
            <strong>{{ __('adminlte::adminlte.category') }}:</strong>
            @if(app()->getLocale()=="ar")
                {{ optional($product->category)->name_ar ?? optional($product->category)->name_en ?? '-' }}
            @else
                {{ optional($product->category)->name_en ?? '-' }}
            @endif
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <strong>{{ __('adminlte::adminlte.description') }} (EN):</strong>
            <p>{{ $product->description_en ?? '-' }}</p>

            <strong>{{ __('adminlte::adminlte.description') }} (AR):</strong>
            <p>{{ $product->description_ar ?? '-' }}</p>
        </div>

        {{-- Colors --}}
        @if(!empty($product->colors) && is_array($product->colors))
            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.colors') }}:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($product->colors as $color)
                        <span class="badge rounded-pill border"
                              style="background-color: {{ $color }}; color: #fff;">
                            {{ $color }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Sizes --}}
        @if($product->sizes && $product->sizes->count())
            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.size') }}:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($product->sizes as $size)
                        @if (app()->getLocale()=="ar")
                            <x-adminlte-badge label="{{ $size->name_ar }}" theme="primary" />
                        @else
                            <x-adminlte-badge label="{{ $size->name_en }}" theme="primary" />
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Additionals --}}
        @if($product->additionals && $product->additionals->count())
            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.additional') }}:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($product->additionals as $additional)
                        @if(app()->getLocale()=="ar")
                            <x-adminlte-badge label="{{ $additional->name_ar }}" theme="info" />
                        @else
                            <x-adminlte-badge label="{{ $additional->name_en }}" theme="info" />
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Images --}}
        @if($product->images && $product->images->count())
            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.image') }}:</strong>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    @foreach($product->images as $image)
                        <div class="border rounded-3 overflow-hidden shadow-sm" style="width:120px; height:120px;">
                            <img src="{{ $image->image_path }}" alt="Product Image"
                                 class="img-fluid object-fit-cover w-100 h-100">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> {{ __('adminlte::adminlte.edit') }}
            </a>
            <a href="{{ route('product.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('adminlte::adminlte.go_back') }}
            </a>
        </div>

    </x-adminlte-card>
</div>

{{-- Listener anchor for window broadcasting --}}
<div id="product-show-listener"
     data-channel="products"
     data-events='["product_updated","ProductUpdated"]'
     data-product-id="{{ $product->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('product-show-listener');
    if (!anchor) {
      console.warn('[product-show] listener anchor not found');
      return;
    }

    const channelName = anchor.dataset.channel || 'products';
    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["product_updated"]');
    } catch (_) {
      events = ['product_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['product_updated'];
    }

    const currentId = anchor.dataset.productId || null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // Accept shapes: { payload: { product: {...} } }, { product: {...} }, or plain
        const raw = e?.payload || e?.product || e;
        const t   = raw?.product || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          // Different product → ignore
          return;
        }

        if (window.toastr) {
          toastr.info(@json(__('adminlte::adminlte.saved_successfully')));
        }

        // Reset page → full reload to get fresh data from server
        window.location.reload();
      };

      // Register for global bootstrapper
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // Subscribe immediately if AppBroadcast is ready
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[product-show] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[product-show] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
