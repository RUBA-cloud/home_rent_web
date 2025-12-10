{{-- resources/views/categories/_form.blade.php --}}
{{-- expects: $action (string), $method ('POST'|'PUT'|'PATCH'),
    $branches (Collection), optional $category (model|null), optional $broadcast --}}

@section('plugins.Select2', true)

@php
    /** @var \App\Models\Category|null $category */
    $category  = $category ?? null;
    $branches  = $branches ?? collect();   // safe default
    $isAr      = app()->getLocale() === 'ar';

    // Broadcasting setup (for live updates)
    $broadcast = $broadcast ?? [
        'channel'        => 'categories',
        'events'         => ['category_updated'],
        'pusher_key'     => config('broadcasting.connections.pusher.key'),
        'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster', 'mt1'),
    ];

    // Build "old selected" branches: prefer old() → then category->branches
    $oldSelected = collect(
        old('branch_ids', $category?->branches?->pluck('id')->all() ?? [])
    )->map(fn ($v) => (int) $v)->values();

    // Safe fallbacks
    /** @var string $action */
    $action = $action ?? url()->current();
    /** @var string $method */
    $method = strtoupper($method ?? ($category?->exists ? 'PUT' : 'POST'));
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="category-form"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast['events'])'>

    @csrf
    @unless (in_array($method, ['GET', 'POST']))
        @method($method)
    @endunless

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Category Image --}}
    <x-upload-image
        :image="$category?->image"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    {{-- Name (English) --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $category?->name_en)"
        rows="1"
    />
    @error('name_en')
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror

    {{-- Name (Arabic) --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', $category?->name_ar)"
        rows="1"
    />
    @error('name_ar')
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror

    {{-- Branches (Multiple Select2) --}}
    @php
        $branchesError = $errors->has('branch_ids') || $errors->has('branch_ids.*');
    @endphp

    <div class="form-group mb-3">
        <label for="branch_ids" class="font-weight-bold mb-2 text-muted">
            {{ __('adminlte::adminlte.branches') }}
        </label>

        <select
            id="branch_ids"
            name="branch_ids[]"
            class="form-control select2 custom-select2 {{ $branchesError ? 'is-invalid' : '' }}"
            multiple
            required
            data-placeholder="{{ __('adminlte::adminlte.select') . ' ' . __('adminlte::adminlte.branches') }}"
            style="width: 100%;">
            @forelse($branches as $branch)
                <option value="{{ $branch->id }}"
                        {{ $oldSelected->contains((int) $branch->id) ? 'selected' : '' }}>
                    {{ $isAr ? ($branch->name_ar ?? $branch->name_en) : ($branch->name_en ?? $branch->name_ar) }}
                </option>
            @empty
                <option value="" disabled>
                    {{ __('adminlte::adminlte.no_records') }}
                </option>
            @endforelse
        </select>

        @error('branch_ids')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
        @error('branch_ids.*')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- Active Checkbox --}}
    <div class="form-group mt-3">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_active"
                   name="is_active"
                   value="1"
                   {{ old('is_active', (int) ($category->is_active ?? 1)) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                {{ __('adminlte::adminlte.is_active') }}
            </label>
        </div>
    </div>
    @error('is_active')
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror

    {{-- Submit Button --}}
    <x-adminlte-button
        :label="$category
            ? __('adminlte::adminlte.update_information')
            : __('adminlte::adminlte.save_information')"
        type="submit"
        theme="success"
        class="w-100 mt-3"
        icon="fas fa-save"
    />
</form>

{{-- === BROADCAST LISTENER ANCHOR === --}}
<div id="category-form-listener"
     data-channel="{{ $broadcast['channel'] }}"
     data-events='@json($broadcast['events'])'>
</div>

@push('css')
<style>
    /* Base selection */
    .select2-container--bootstrap4 .select2-selection {
        min-height: 38px;
        border-radius: .35rem;
        border-color: #ced4da;
        display: flex;
        align-items: center;
        padding: 2px 6px;
    }

    /* Focus state */
    .select2-container--bootstrap4.select2-container--focus .select2-selection {
        box-shadow: 0 0 0 0.1rem rgba(40, 167, 69, 0.25);
        border-color: #28a745;
    }

    /* Placeholder text */
    .select2-container--bootstrap4 .select2-selection__placeholder {
        color: #6c757d;
        opacity: 0.9;
    }

    /* Multiple selected tags (chips) */
    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
        background-color: #e9f7ef;
        border: 1px solid #28a74533;
        color: #155724;
        border-radius: 20px;
        padding: 2px 10px;
        margin-top: 3px;
        margin-bottom: 3px;
        font-size: 0.85rem;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
        color: #155724;
        margin-right: 4px;
        margin-left: 2px;
    }

    /* Error state match .is-invalid on original select */
    select.is-invalid + .select2-container--bootstrap4 .select2-selection {
        border-color: #e3342f !important;
        box-shadow: 0 0 0 0.1rem rgba(227, 52, 47, .25);
    }

    /* RTL tweaks */
    [dir="rtl"] .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
        direction: rtl;
    }

    [dir="rtl"] .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
        margin-left: 4px;
        margin-right: 2px;
    }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

    // ====== Select2 INIT ======
    const $branchSelect = $('#branch_ids');

    if ($branchSelect.length) {
        $branchSelect.select2({
            theme: 'bootstrap4',
            width: 'resolve', // respects style="width: 100%"
            dir: isRtl ? 'rtl' : 'ltr',
            placeholder: $branchSelect.data('placeholder') || '',
            allowClear: true,
        });
    }

    // ====== BROADCAST LIKE companyInfo ======
    const form   = document.getElementById('category-form');
    const anchor = document.getElementById('category-form-listener');

    if (!form || !anchor) {
        console.warn('[category-form] form or listener anchor not found');
        return;
    }

    const channelName = anchor.dataset.channel || 'categories';

    let events;
    try {
        events = JSON.parse(anchor.dataset.events || '["category_updated"]');
    } catch (_) {
        events = ['category_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
        events = ['category_updated'];
    }

    function setField(name, value) {
        if (value === undefined || value === null) return;
        const el = form.querySelector('[name="'+name+'"]');
        if (!el) return;
        el.value = value;
        el.dispatchEvent(new Event('input',  { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function applyCategoryPayload(payload) {
        // Accept { category: {...} } or direct {...}
        const c = payload?.category ?? payload ?? {};

        console.log('[category-form] applying broadcast payload:', c);

        // Basic fields
        if (c.name_en !== undefined) setField('name_en', c.name_en);
        if (c.name_ar !== undefined) setField('name_ar', c.name_ar);

        // is_active
        if (c.is_active !== undefined) {
            const cb = form.querySelector('#is_active');
            if (cb) {
                const on = Number(c.is_active) === 1;
                cb.checked = on;
            }
        }

        // Image preview
        if (c.image_url || c.image) {
            const src = c.image_url || c.image;
            const img = document.querySelector('#image-preview, [data-role="image-preview"]');
            if (img && src) {
                img.src = src;
            }
        }

        // Branches (supports branch_ids array OR branches objects)
        if (c.branch_ids || c.branches) {
            let ids = [];

            if (Array.isArray(c.branch_ids)) {
                ids = c.branch_ids.map(v => String(v));
            } else if (Array.isArray(c.branches)) {
                ids = c.branches.map(b => String(b.id));
            }

            if ($branchSelect && ids.length) {
                $branchSelect.val(ids).trigger('change');
            }
        }

        if (window.toastr) {
            toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }
    }

    // Register with global AppBroadcast (same pattern as companyInfo)
    window.AppBroadcast = window.AppBroadcast || [];

    events.forEach(function (ev) {
        const entry = {
            channel: channelName,
            event:   ev,
            handler: applyCategoryPayload,
        };

        // If manager object: use subscribe()
        if (typeof window.AppBroadcast.subscribe === 'function') {
            window.AppBroadcast.subscribe(channelName, ev, applyCategoryPayload);
            console.info('[category-form] listening via AppBroadcast →', channelName, '/', ev);
        } else {
            // Pre-manager phase: push to array, layout's JS will boot them
            window.AppBroadcast.push(entry);
            console.info('[category-form] registered broadcast entry →', channelName, '/', ev);
        }
    });
});
</script>
@endpush
