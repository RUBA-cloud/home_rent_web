{{-- resources/views/company_branches/_form.blade.php --}}
{{-- expects: $action (string), $method (POST|PUT|PATCH), optional $branch (model|null) --}}

@php
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      style="margin:10px"
      id="company-branch-form"
      data-channel="company_branch"
      data-events='@json(["company_branch_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET','POST']))
        @method($method)
    @endunless

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- Logo --}}
    <x-upload-image :image="$branch->image ?? null"
        label="{{ __('adminlte::adminlte.choose_file') }}"
        name="image" id="logo" />

    {{-- Names --}}
    <x-form.textarea id="name_en" name="name_en"
        label="{{ __('adminlte::adminlte.branch_name_en') }}"
        :value="old('name_en', $branch->name_en ?? '')" rows="1" />

    <x-form.textarea id="name_ar" name="name_ar" dir="rtl"
        label="{{ __('adminlte::adminlte.branch_name_ar') }}"
        :value="old('name_ar', $branch->name_ar ?? '')" rows="1" />

    {{-- Contact --}}
    <x-form.textarea id="phone" name="phone"
        label="{{ __('adminlte::adminlte.branch_phone') }}"
        :value="old('phone', $branch->phone ?? '')" rows="1" />

    <x-form.textarea id="email" name="email"
        label="{{ __('adminlte::adminlte.company_email') }}"
        :value="old('email', $branch->email ?? '')" rows="1" />

    {{-- Address --}}
    <x-form.textarea id="address_en" name="address_en"
        label="{{ __('adminlte::adminlte.company_address_en') }}"
        :value="old('address_en', $branch->address_en ?? '')" rows="1" />

    <x-form.textarea id="address_ar" name="address_ar"
        label="{{ __('adminlte::adminlte.company_address_ar') }}"
        :value="old('address_ar', $branch->address_ar ?? '')" rows="1" />

    <x-form.textarea id="fax" name="fax"
        label="{{ __('adminlte::adminlte.fax') }}"
        :value="old('fax', $branch->fax ?? '')" rows="1" />

    <x-form.textarea id="location" name="location"
        label="{{ __('adminlte::adminlte.location') }}"
        :value="old('location', $branch->location ?? '')" rows="1" />

    {{-- ✅ Working days & hours (EDIT-SAFE) --}}
    <x-working-days-hours
        :branch="$branch ?? null"
        :branch_working_days="old('working_days', $branch->working_days ?? [])"
        :branch_working_hours_from="old('working_hours_from', $branch->working_hours_from ?? '')"
        :branch_working_hours_to="old('working_hours_to', $branch->working_hours_to ?? '')"
        label="{{ __('adminlte::adminlte.working_days_hours') }}"
    />

    {{-- Active --}}
    <div class="form-group" style="margin:20px 0;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', isset($branch) ? (int)$branch->is_active : 0) ? 'checked' : '' }} />
        {{ __('adminlte::adminlte.active') }}
    </div>

    <x-adminlte-button
        :label="__('adminlte::adminlte.save_information')"
        type="submit" theme="success" class="w-100" icon="fas fa-save" />
</form>

@push('js')
@once
<script>
(function(){
  'use strict';

  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;

  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setCheckbox = (name, isOn) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(isOn);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const previewLogo = (url) => {
    if (!url) return;
    const img = document.querySelector('#logo-preview, [data-role="logo-preview"]');
    if (img) img.src = url;
  };

  const applyPayload = (payload) => {
    const b = payload?.branch ?? payload ?? {};

    setField('name_en',     b.name_en);
    setField('name_ar',     b.name_ar);
    setField('email',       b.email);
    setField('phone',       b.phone);
    setField('address_en',  b.address_en);
    setField('address_ar',  b.address_ar);
    setField('location',    b.location);
    setField('fax',         b.fax);
    setCheckbox('is_active', b.is_active);

    // ✅ Working hours (hidden + visible)
    if (b.working_hours_from) {
        setField('working_hours_from_visible', b.working_hours_from);
        const hiddenFrom = document.getElementById('branch_working_hours_from');
        if (hiddenFrom) hiddenFrom.value = b.working_hours_from;
    }
    if (b.working_hours_to) {
        setField('working_hours_to_visible', b.working_hours_to);
        const hiddenTo = document.getElementById('branch_working_hours_to');
        if (hiddenTo) hiddenTo.value = b.working_hours_to;
    }

    // ✅ Working days checkboxes (expects array of keys like ["sat","sun"])
    if (Array.isArray(b.working_days)) {
        document.querySelectorAll('input[name="working_days[]"]').forEach(ch => {
            const key = ch.dataset.key || ch.value;
            ch.checked = b.working_days.includes(key);
        });
    }

    previewLogo(b.image_url || b.logo_url || b.image);

    if (window.bsCustomFileInput && document.querySelector('input[type="file"][name="image"]')) {
      try { bsCustomFileInput.init(); } catch (_) {}
    }

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_) {}
    }

    console.log('[company_branch] form updated from broadcast payload', b);
  };

  // Expose if you ever want to call it manually
  window.updateCompanyBranchForm = applyPayload;

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('company-branch-form');
    if (!form) {
      console.warn('[company_branch] form not found');
      return;
    }

    // ---- Register with global broadcasting (same style as additional show) ----
    window.__pageBroadcasts = window.__pageBroadcasts || [];

    const handler = function (e) {
      applyPayload(e && (e.branch ?? e));
    };

    window.__pageBroadcasts.push({
      channel: 'company_branch',          // broadcastOn()
      event:   'company_branch_updated',  // broadcastAs()
      handler: handler
    });

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe('company_branch', 'company_branch_updated', handler);
      console.info('[company_branch] subscribed via AppBroadcast → company_branch / company_branch_updated');
    } else {
      console.info('[company_branch] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endonce
@endpush
