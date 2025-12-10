@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_info'))

@section('content')
<div class="container-fluid py-4">

 <x-adminlte-card   title="{{ __('adminlte::adminlte.company_info') }}"  icon="fas fa-building"   removable collapsible class="lw-list-card">
        <div class="d-flex flex-wrap justify-content-end align-items-center mt-4">
            <a href="{{ route('companyInfo.history', ['isHistory' => true]) }}" class="btn btn-outline-secondary mr-2" target="_blank" title="{{ __('adminlte::adminlte.history') }}">
                <i class="fas fa-history"></i> {{ __('adminlte::adminlte.history') }}
            </a>
        </div>

        <form method="POST" action="{{ route('companyInfo.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Company Logo --}}
            <x-upload-image
                :image="$company->image ?? null"
                label="{{ __('adminlte::adminlte.choose_file') }}"
                name="image"
                id="logo" />

            {{-- Company Information Fields (using text component) --}}
            <x-form.textarea
                name="name_en"
                label="{{ __('adminlte::adminlte.company_name_en') }}"
                dir="ltr"
                placeholder="{{ __('adminlte::adminlte.company_name_en_placeholder') }}"
                :value="data_get($company, 'name_en')" />

            <x-form.textarea
                name="name_ar"
                label="{{ __('adminlte::adminlte.company_name_ar') }}"
                dir="rtl"
                placeholder="{{ __('adminlte::adminlte.company_name_ar_placeholder') }}"
                :value="data_get($company, 'name_ar')" />

            <x-form.textarea
                name="email"
                label="{{ __('adminlte::adminlte.company_email') }}"
                type="email"
                dir="ltr"
                placeholder="{{ __('adminlte::adminlte.company_email_placeholder') }}"
                :value="data_get($company, 'email')" />

            <x-form.textarea
                name="phone"
                label="{{ __('adminlte::adminlte.company_phone') }}"
                type="text"
                dir="ltr"
                placeholder="{{ __('adminlte::adminlte.company_phone_placeholder') }}"
                :value="data_get($company, 'phone')" />

            <x-form.textarea
                name="address_en"
                label="{{ __('adminlte::adminlte.company_address_en') }}"
                dir="ltr"
                :value="data_get($company, 'address_en')" />

            <x-form.textarea
                name="address_ar"
                label="{{ __('adminlte::adminlte.company_address_ar') }}"
                dir="rtl"
                :value="data_get($company, 'address_ar')" />

            <x-form.textarea
                name="location"
                label="{{ __('adminlte::adminlte.company_location') }}"
                dir="ltr"
                placeholder="{{ __('adminlte::adminlte.company_location_placeholder') }}"
                :value="data_get($company, 'location')" />

            <x-form.textarea
                name="about_us_en"
                label="{{ __('adminlte::adminlte.about_us_en') }}"
                dir="ltr"
                :value="data_get($company, 'about_us_en')" />

            <x-form.textarea
                name="about_us_ar"
                label="{{ __('adminlte::adminlte.about_us_ar') }}"
                dir="rtl"
                :value="data_get($company, 'about_us_ar')" />

            <x-form.textarea
                name="mission_en"
                label="{{ __('adminlte::adminlte.mission_en') }}"
                dir="ltr"
                :value="data_get($company, 'mission_en')" />

            <x-form.textarea
                name="mission_ar"
                label="{{ __('adminlte::adminlte.mission_ar') }}"
                dir="rtl"
                :value="data_get($company, 'mission_ar')" />

            <x-form.textarea
                name="vision_en"
                label="{{ __('adminlte::adminlte.vision_en') }}"
                dir="ltr"
                :value="data_get($company, 'vision_en')" />

            <x-form.textarea
                name="vision_ar"
                label="{{ __('adminlte::adminlte.vision_ar') }}"
                dir="rtl"
                :value="data_get($company, 'vision_ar')" />

            {{-- Colors --}}
            @php
              $colors = $colors ?? [
                ['name' => 'main_color',        'label' => __('adminlte::adminlte.main_color')],
                ['name' => 'sub_color',         'label' => __('adminlte::adminlte.sub_color')],
                ['name' => 'text_color',        'label' => __('adminlte::adminlte.text_color')],
                ['name' => 'button_color',      'label' => __('adminlte::adminlte.button_color')],
                ['name' => 'button_text_color', 'label' => __('adminlte::adminlte.button_text_color')],
                ['name' => 'icon_color',        'label' => __('adminlte::adminlte.icon_color')],
                ['name' => 'text_filed_color',  'label' => __('adminlte::adminlte.text_field_color')],
                ['name' => 'card_color',        'label' => __('adminlte::adminlte.card_color')],
                ['name' => 'label_color',       'label' => __('adminlte::adminlte.label_color')],
                ['name' => 'hint_color',        'label' => __('adminlte::adminlte.hint_color')],
              ];
            @endphp

            <div class="row">
              @foreach($colors as $c)
                <div class="col-sm-6 col-md-4 mb-3">
                  <x-adminlte-input
                      name="{{ $c['name'] }}"
                      label="{{ $c['label'] }}"
                      type="color"
                      igroup-size="sm"
                      value="{{ old($c['name'], data_get($company, $c['name']) ?? '#ffffff') }}"/>
                </div>
              @endforeach
            </div>

            <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit"
                theme="success"
                class="full-width-btn ="
                icon="fas fa-save" />
        </form>
    </x-adminlte-card>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${name}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setColorsIfAny = (obj) => {
    [
      'main_color','sub_color','text_color','button_color',
      'button_text_color','icon_color','text_filed_color',
      'card_color','label_color','hint_color'
    ].forEach(n => setField(n, obj?.[n]));
  };

  const applyPayload = (payload) => {
    const data = payload?.company ?? payload ?? {};

    setField('name_en',     data.name_en);
    setField('name_ar',     data.name_ar);
    setField('email',       data.email);
    setField('phone',       data.phone);
    setField('address_en',  data.address_en);
    setField('address_ar',  data.address_ar);
    setField('location',    data.location);
    setField('about_us_en', data.about_us_en);
    setField('about_us_ar', data.about_us_ar);
    setField('mission_en',  data.mission_en);
    setField('mission_ar',  data.mission_ar);
    setField('vision_en',   data.vision_en);
    setField('vision_ar',   data.vision_ar);

    setColorsIfAny(data);

    const src = data.image_url || data.logo_url;
    if (src) {
      const img = document.querySelector('#logo-preview, [data-role="logo-preview"]');
      if (img) img.src = src;
    }

    if (window.bsCustomFileInput && document.querySelector('input[type="file"][name="image"]')) {
      bsCustomFileInput.init();
    }

    if (window.toastr) {
      toastr.success(@json(__('adminlte::adminlte.company_info_updated') ?? 'Company info updated.'));
    }

    console.log('[company_info] patched company form from payload', data);
  };

  window.AppBroadcast = window.AppBroadcast || [];
  window.AppBroadcast.push({
    channel: 'company_info',
    event:   'company_info_updated',
    handler: applyPayload
  });

  if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
    window.AppBroadcast.subscribe('company_info', 'company_info_updated', applyPayload);

    console.info('[company_info] subscribed via AppBroadcast → company_info / company_info_updated');
  } else {
    console.info('[company_info] registered in __pageBroadcasts; layout will subscribe later.');
  }
});
</script>
@endpush
