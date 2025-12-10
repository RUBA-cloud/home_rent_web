@extends('adminlte::page')

@section('title', __('adminlte::adminlte.category'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-code-branch me-2 text-primary"></i>
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.category') }}
            @else
                {{ __('adminlte::adminlte.category') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>

        <span id="category-main-badge"
              class="badge bg-purple text-white px-3 py-2 {{ $category->is_main_branch ? '' : 'd-none' }}">
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
                        $imgSrc = $category->image
                            ? asset($category->image)
                            : 'https://placehold.co/500x300?text=Branch+Image';
                    @endphp
                    <img
                        id="category-image"
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

                    {{-- Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div id="category-name-en" class="fs-5 fw-bold text-dark">
                            {{ $category->name_en }}
                        </div>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div id="category-name-ar" class="fs-5 fw-bold text-dark">
                            {{ $category->name_ar }}
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        <span id="category-status"
                              class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                            @if($category->is_active)
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            @else
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            @endif
                        </span>
                    </div>

                    {{-- Addresses --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_en') }}</small>
                        <div id="category-address-en" class="fw-semibold">
                            {{ $category->address_en ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_ar') }}</small>
                        <div id="category-address-ar" class="fw-semibold">
                            {{ $category->address_ar ?? '-' }}
                        </div>
                    </div>

                    {{-- Branches --}}
                    <div class="col-12">
                        <h6 class="font-weight-bold text-secondary">{{ __('adminlte::menu.branches') }}</h6>
                        <div id="category-branches">
                            @if($category->branches->count())
                                <ul class="list-unstyled ps-2">
                                    @foreach($category->branches as $branch)
                                        <li>
                                            <a href="{{ route('companyBranch.show', $branch->id) }}" class="text-primary fw-bold">
                                                @if(app()->getLocale() == 'ar')
                                                    <i class="fas fa-code-branch me-1"></i> {{ $branch->name_ar }}
                                                @else
                                                    <i class="fas fa-code-branch me-1"></i> {{ $branch->name_en }}
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">{{ __('adminlte::adminlte.no_branches') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </x-adminlte-card>
</div>

{{-- Listener anchor for Pusher --}}
<div id="category-listener"
     data-channel="categories"
     data-events='["category_updated","CategoryUpdated"]'
     data-pusher-key="{{ config('broadcasting.connections.pusher.key') }}"
     data-pusher-cluster="{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}"
     data-category-id="{{ $category->id }}">
</div>
@endsection

@section('js')
<script>
(function () {
  'use strict';

  // Normalize → string
  function norm(v) {
    if (v === undefined || v === null) return '';
    return String(v);
  }

  // Re-render branches list if payload includes branches
  function renderBranches(container, branches) {
    if (!container) return;

    if (!Array.isArray(branches) || branches.length === 0) {
      container.innerHTML = '<p class="text-muted mb-0">{{ __('adminlte::adminlte.no_branches') }}</p>';
      return;
    }

    const ul = document.createElement('ul');
    ul.className = 'list-unstyled ps-2';

    branches.forEach(b => {
      const li  = document.createElement('li');
      const a   = document.createElement('a');
      a.className = 'text-primary fw-bold';
      a.href = '{{ route('companyBranch.show', '__ID__') }}'.replace('__ID__', b.id);

      // We'll just show both names if available
      const label = '{{ app()->getLocale() == "ar" ? "__AR__" : "__EN__" }}'
        .replace('__AR__', norm(b.name_ar || b.name_en))
        .replace('__EN__', norm(b.name_en || b.name_ar));

      a.innerHTML = '<i class="fas fa-code-branch me-1"></i> ' + label;
      li.appendChild(a);
      ul.appendChild(li);
    });

    container.innerHTML = '';
    container.appendChild(ul);
  }

  // Update the visible text / badges / image from payload
  function updateDomFromPayload(payload) {
    if (!payload) return;

    const c = payload.category ?? payload ?? {};

    // Only update if this is the same category
    const anchor = document.getElementById('category-listener');
    const currentId = anchor ? anchor.dataset.categoryId : null;
    if (currentId && c.id && String(c.id) !== String(currentId)) {
      return;
    }

    // Names
    const nameEnEl = document.getElementById('category-name-en');
    if (nameEnEl) nameEnEl.textContent = norm(c.name_en);

    const nameArEl = document.getElementById('category-name-ar');
    if (nameArEl) nameArEl.textContent = norm(c.name_ar);

    // Status
    const statusEl = document.getElementById('category-status');
    if (statusEl && c.is_active !== undefined && c.is_active !== null) {
      const isOn = Number(c.is_active) === 1;
      statusEl.classList.remove('bg-success', 'bg-danger');
      statusEl.classList.add(isOn ? 'bg-success' : 'bg-danger');
      statusEl.innerHTML = isOn
        ? '<i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}'
        : '<i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}';
    }

    // Main branch badge
    const mainBadge = document.getElementById('category-main-badge');
    if (mainBadge && c.is_main_branch !== undefined && c.is_main_branch !== null) {
      const isMain = Number(c.is_main_branch) === 1;
      if (isMain) {
        mainBadge.classList.remove('d-none');
      } else {
        mainBadge.classList.add('d-none');
      }
    }

    // Addresses
    const addrEnEl = document.getElementById('category-address-en');
    if (addrEnEl) addrEnEl.textContent = norm(c.address_en) || '-';

    const addrArEl = document.getElementById('category-address-ar');
    if (addrArEl) addrArEl.textContent = norm(c.address_ar) || '-';

    // Image
    const imgEl = document.getElementById('category-image');
    if (imgEl) {
      const newSrc = c.image_url || c.image || imgEl.dataset.placeholder;
      if (newSrc) imgEl.src = newSrc;
    }

    // Branches
    const branchesContainer = document.getElementById('category-branches');
    if (Array.isArray(c.branches)) {
      renderBranches(branchesContainer, c.branches);
    }

    if (window.toastr) {
      toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
    }

    console.log('[categories] show updated from payload', c);
  }

  // Expose globally if needed
  window.updateCategoryShow = updateDomFromPayload;

  // Dynamically load Pusher if needed
  function loadPusher() {
    return new Promise(function (resolve, reject) {
      if (window.Pusher) return resolve();
      const s = document.createElement('script');
      s.src = 'https://js.pusher.com/8.4/pusher.min.js';
      s.async = true;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('category-listener');
    if (!anchor) {
      console.warn('[categories] listener anchor not found');
      return;
    }

    const channelName = anchor.dataset.channel || 'categories';

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["category_updated"]');
    } catch (_) {
      events = ['category_updated'];
    }
    if (!Array.isArray(events) || events.length === 0) {
      events = ['category_updated'];
    }

    let key     = anchor.dataset.pusherKey || '';
    let cluster = anchor.dataset.pusherCluster || 'mt1';

    if (!key) {
      const mk = document.querySelector('meta[name="pusher-key"]');
      key = mk ? mk.content : '';
    }
    if (!cluster) {
      const mc = document.querySelector('meta[name="pusher-cluster"]');
      cluster = mc ? mc.content : 'mt1';
    }

    if (!key) {
      console.warn('[categories] Missing Pusher key');
      return;
    }

    loadPusher()
      .then(function () {
        // eslint-disable-next-line no-undef
        const pusher = new Pusher(key, { cluster: cluster, forceTLS: true });
        const ch = pusher.subscribe(channelName);

        events.forEach(function (ev) {
          // exact
          ch.bind(ev, function (e) {
            updateDomFromPayload(e.category || e);
          });
          // lowercase
          ch.bind(ev.toLowerCase(), function (e) {
            updateDomFromPayload(e.category || e);
          });
          // dotted (Laravel echo style)
          ch.bind('.' + ev, function (e) {
            updateDomFromPayload(e.category || e);
          });
        });

        console.info('[categories] listening on "' + channelName + '" for', events);
      })
      .catch(function (err) {
        console.error('[categories] failed to load/init Pusher', err);
      });
  });
})();
</script>
@endsection
