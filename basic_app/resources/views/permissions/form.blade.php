{{-- resources/views/permissions/_form.blade.php --}}
@php
    use Illuminate\Support\Str;

    $isRtl = app()->isLocale('ar');

    // Inputs expected:
    // $action, $method
    // Optional: $permission, $featuresForRadios, $defaultFeatureKey, $module, $channel, $events
    $featuresForRadios = $featuresForRadios ?? [];
    $permissionObj     = $permission ?? null;

    // helper to keep old() → model → default
    $checked = fn ($f) => old($f, data_get($permissionObj, $f, false)) ? 'checked' : '';

    // selected feature
    $selectedFeature = (string) old('module_name', (string) ($defaultFeatureKey ?? ''));

    // window broadcasting defaults
    $broadcast = $broadcast ?? [
        'channel' => 'permissions',
        'events'  => ['permissions_updated'],
    ];
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="permission-form"
      class="permission-form-wrapper"
      dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast['events'])'>
    @csrf
    @unless (in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endunless

    @if(!empty($module?->id))
        <input type="hidden" name="module_id" value="{{ $module->id }}">
    @endif

    <style>
        .permission-form-wrapper {
            margin: 10px;
        }

        /* ===== card shell ===== */
        .perm-card {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(148, 163, 184, .25);
        }
        .perm-card-header {
            background: linear-gradient(135deg, #6366f1 0%, #0ea5e9 60%, #14b8a6 100%);
            padding: 1.1rem 1.5rem 1rem;
            color: #fff;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
        }
        .perm-card-title {
            font-weight: 700;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .perm-badge-muted {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.35);
            border-radius: 999px;
            padding: .15rem .7rem;
            font-size: .7rem;
        }
        .perm-card-body {
            padding: 1.2rem 1.5rem 1.3rem;
        }

        /* ===== main grid ===== */
        .perm-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr; /* LTR */
            gap: 1.25rem;
        }
        [dir="rtl"] .perm-grid {
            grid-template-columns: .9fr 1.1fr; /* RTL → swap columns */
        }
        @media (max-width: 991.98px) {
            .perm-grid,
            [dir="rtl"] .perm-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ===== radios (modules) ===== */
        .radio-grid {
            display:grid;
            gap:.75rem;
            grid-template-columns: 1fr;
        }
        .radio-card {
            border:1px solid #e2e8f0;
            border-radius:14px;
            padding:12px 14px;
            display:flex;
            align-items:flex-start;
            gap:12px;
            background:#fff;
            transition: all .15s ease;
            cursor:pointer;
            position: relative;
        }
        .radio-card:hover {
            background:#f8fafc;
            border-color:#cbd5f5;
        }
        .radio-card:has(input:checked) {
            border-color:#6366f1;
            box-shadow:0 0 0 3px rgba(99, 102, 241, .12);
            background: #eef2ff;
        }
        .radio-card-input {
            margin-top:4px;
            flex:0 0 auto;
            accent-color:#6366f1;
            margin-right:.5rem;
        }
        [dir="rtl"] .radio-card {
            flex-direction: row-reverse;
            text-align: right;
        }
        [dir="rtl"] .radio-card-input {
            margin-right:0;
            margin-left:.5rem;
        }
        .radio-card-body { flex:1 1 auto; }
        .radio-card-title {
            font-weight:600;
            line-height:1.3;
            color:#0f172a;
            display:flex;
            gap:.5rem;
            align-items:center;
        }
        .radio-card-desc {
            font-size:.75rem;
            color:#64748b;
            margin-top:4px;
        }
        .radio-badge {
            display:inline-flex;
            align-items:center;
            gap:6px;
            font-size:.65rem;
            padding:2px 8px;
            border-radius:999px;
            background:#e0e7ff;
            color:#3730a3;
            border:1px solid #c7d2fe;
        }
        .radio-icon {
            width:26px;
            height:26px;
            border-radius:999px;
            background: rgba(99,102,241,.12);
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:#6366f1;
            font-size:.7rem;
        }

        /* ===== right column blocks ===== */
        .perm-field-group {
            background: #f8fafc;
            border: 1px solid rgba(148, 163, 184, .25);
            border-radius: 14px;
            padding: 1rem 1rem .7rem;
        }
        .perm-section-title {
            font-weight: 600;
            font-size: .87rem;
            color: #0f172a;
            display:flex;
            align-items:center;
            gap:.35rem;
            margin-bottom: .7rem;
        }
        [dir="rtl"] .perm-section-title {
            flex-direction: row-reverse;
        }
        .perm-section-hint {
            font-size: .7rem;
            color: #94a3b8;
            margin-inline-start: .4rem;
        }
        .perm-input {
            border-radius: .75rem !important;
            border: 1px solid rgba(148, 163, 184, .5);
        }
        .perm-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }

        /* ===== pills (capabilities) ===== */
        .perm-toggle-list {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem .6rem;
        }
        .perm-toggle {
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, .35);
            border-radius: 999px;
            padding: .3rem .75rem;
            display:inline-flex;
            align-items:center;
            gap:.4rem;
            cursor:pointer;
            transition: all .12s ease;
        }
        .perm-toggle input[type="checkbox"] {
            margin:0;
            accent-color:#6366f1;
        }
        .perm-toggle:hover {
            background:#eff6ff;
            border-color:#bfdbfe;
        }
        .perm-toggle input:checked + span {
            font-weight:600;
            color:#0f172a;
        }
        [dir="rtl"] .perm-toggle {
            flex-direction: row-reverse;
        }

        /* active checkbox */
        .perm-check-stack .form-check {
            display:flex;
            align-items:center;
            gap:.45rem;
            margin-bottom:.35rem;
        }
        .perm-check-stack .form-check-input {
            margin:0;
            accent-color:#6366f1;
        }
        [dir="rtl"] .perm-check-stack .form-check {
            flex-direction: row-reverse;
            text-align: right;
        }

        /* footer */
        .perm-actions {
            padding: 0 1.5rem 1.25rem;
            display:flex;
            justify-content:flex-end;
        }
        [dir="rtl"] .perm-actions {
            justify-content:flex-start;
        }

        /* header RTL */
        [dir="rtl"] .perm-card-header {
            flex-direction: row-reverse;
        }
    </style>

    <div class="perm-card">
        <div class="perm-card-header">
            <div class="perm-card-title">
                <span class="badge bg-white text-dark rounded-circle"
                      style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;">
                    <i class="fas fa-lock"></i>
                </span>
                <div>
                    {{ __('adminlte::adminlte.permissions') }}
                    <div class="small" style="opacity:.75;">
                        {{ $permissionObj?->name_en ? __('adminlte::adminlte.edit') : __('adminlte::adminlte.create') }}
                    </div>
                </div>
            </div>
            <span class="perm-badge-muted">
                <i class="fas fa-history"></i> {{ now()->format('Y-m-d') }}
            </span>
        </div>

        <div class="perm-card-body">
            <div class="perm-grid">
                {{-- COLUMN 1: features/modules --}}
                <div>
                    <div class="perm-section-title mb-2">
                        <i class="fas fa-layer-group"></i>
                        <span>{{ __('adminlte::adminlte.capabilities') }}</span>
                        <span class="perm-section-hint">
                            {{ __('adminlte::adminlte.choose_feature') !== 'adminlte::adminlte.choose_feature'
                                 ? __('adminlte::adminlte.choose_feature')
                                 : '' }}
                        </span>
                    </div>

                    @if(!empty($featuresForRadios))
                        <div class="radio-grid">
                            @foreach($featuresForRadios as $key => $meta)
                                @php
                                    $label = is_array($meta) ? ($meta['label'] ?? (string)$key) : (string)$meta;
                                    $desc  = is_array($meta) ? ($meta['desc']  ?? null)        : null;
                                    $badge = is_array($meta) ? ($meta['badge'] ?? null)        : null;
                                    $icon  = is_array($meta) ? ($meta['icon']  ?? null)        : null;
                                    $id    = 'feature_'.Str::slug((string)$key, '_');
                                @endphp

                                <label class="radio-card" for="{{ $id }}">
                                    <input
                                        type="radio"
                                        name="module_name"
                                        id="{{ $id }}"
                                        value="{{ $key }}"
                                        class="radio-card-input form-check-input"
                                        {{ $selectedFeature === (string)$key ? 'checked' : '' }}
                                    />
                                    <div class="radio-card-body">
                                        <div class="d-flex align-items-center-gap-2">
                                            <div class="radio-card-title">
                                                <span class="radio-icon">
                                                    <i class="fas fa-{{ $icon ?? 'cubes' }}"></i>
                                                </span>
                                                {{ $label }}
                                            </div>
                                            @if($badge)
                                                <span class="radio-badge">
                                                    <i class="fas fa-star" aria-hidden="true"></i> {{ $badge }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($desc)
                                            <div class="radio-card-desc">{{ $desc }}</div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            {{ __('adminlte::adminlte.no_features') }}
                        </div>
                    @endif

                    @error('module_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    @error('module_id')   <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                {{-- COLUMN 2: form fields --}}
                <div>
                    <div class="perm-field-group mb-3">
                        <div class="perm-section-title">
                            <i class="fas fa-info-circle"></i>
                            {{ __('adminlte::adminlte.basic_information') !== 'adminlte::adminlte.basic_information'
                                 ? __('adminlte::adminlte.basic_information')
                                 : 'Basic information' }}
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">{{ __('adminlte::adminlte.name_en') }}</label>
                            <input type="text" name="name_en" class="form-control perm-input"
                                   value="{{ old('name_en', data_get($permissionObj, 'name_en', '')) }}" required>
                            @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div>
                            <label class="form-label mb-1">{{ __('adminlte::adminlte.name_ar') }}</label>
                            <input type="text" name="name_ar" class="form-control perm-input"
                                   value="{{ old('name_ar', data_get($permissionObj, 'name_ar', '')) }}" required>
                            @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="perm-field-group">
                        <div class="perm-section-title">
                            <i class="fas fa-user-shield"></i>
                            {{ __('adminlte::adminlte.capabilities') }}
                        </div>

                        <div class="perm-toggle-list mb-3">
                            <label class="perm-toggle">
                                <input type="checkbox" name="can_edit" id="can_edit" value="1" {{ $checked('can_edit') }}>
                                <span><i class="fas fa-pen-to-square"></i> {{ __('adminlte::adminlte.edit') }}</span>
                            </label>
                            <label class="perm-toggle">
                                <input type="checkbox" name="can_delete" id="can_delete" value="1" {{ $checked('can_delete') }}>
                                <span><i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}</span>
                            </label>
                            <label class="perm-toggle">
                                <input type="checkbox" name="can_add" id="can_add" value="1" {{ $checked('can_add') }}>
                                <span><i class="fas fa-plus"></i> {{ __('adminlte::adminlte.add') }}</span>
                            </label>
                            <label class="perm-toggle">
                                <input type="checkbox" name="can_view_history" id="can_view_history" value="1" {{ $checked('can_view_history') }}>
                                <span><i class="fas fa-clock-rotate-left-m5"></i> {{ __('adminlte::adminlte.view_history') }}</span>
                            </label>
                        </div>

                        <div class="perm-check-stack">
                            @php $isActive = old('is_active', data_get($permissionObj, 'is_active', 1)); @endphp
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       class="form-check-input" {{ $isActive ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label" style="margin: 5px">{{ __('adminlte::adminlte.active') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{-- /perm-grid --}}
        </div>

        <div class="perm-actions">
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-save {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('adminlte::adminlte.save_information') !== 'adminlte::adminlte.save_information'
                    ? __('adminlte::adminlte.save_information')
                    : __('adminlte::adminlte.save') }}
            </button>
        </div>
    </div>
</form>

@push('js')
<script>
(function () {
  'use strict';

  const form = document.getElementById('permission-form');
  if (!form) {
    console.warn('[permissions-form] form not found');
    return;
  }

  function applyPayloadToForm(payload) {
    if (!payload || typeof payload !== 'object') return;

    Object.entries(payload).forEach(([name, value]) => {
      const inputs = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
      if (!inputs.length) return;

      inputs.forEach((el) => {
        const type = (el.getAttribute('type') || el.tagName).toLowerCase();

        if (type === 'radio') {
          el.checked = (String(el.value) === String(value));
        } else if (type === 'checkbox') {
          el.checked = Boolean(value) && String(value) !== '0';
        } else {
          el.value = (value ?? '');
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    const ds          = form.dataset;
    const channelName = ds.channel || 'permissions';

    let events;
    try {
      events = JSON.parse(ds.events || '["permissions_updated"]');
    } catch (_) {
      events = ['permissions_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['permissions_updated'];
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // support shapes: { payload: {...} } or { permission: {...} } or plain object
        const payload = e?.payload || e?.permission || e;
        applyPayloadToForm(payload);

        form.classList.add('border', 'border-success');
        setTimeout(() => form.classList.remove('border', 'border-success'), 800);
      };

      // register for global bootstrapper
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // immediate subscribe if AppBroadcast is ready
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[permissions-form] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[permissions-form] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
