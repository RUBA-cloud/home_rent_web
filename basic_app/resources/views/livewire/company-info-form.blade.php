<!-- resources/views/livewire/company-info-form.blade.php -->
<form wire:submit.prevent="save"  enctype="multipart/form-data">
    @csrf

    {{-- Company Logo --}}
    @if (class_exists('App\\View\\Components\\UploadImage'))
        <x-upload-image
            :image="$company->image ?? null"
            label="{{ __('adminlte::adminlte.choose_file') }}"
            name="image" id="logo" wire:model="image" />
    @else
        <x-adminlte-input-file
            name="image" igroup-size="sm"
            label="{{ __('adminlte::adminlte.choose_file') }}"
            wire:model="image" />
        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
    @endif

    <x-adminlte-textarea name="name_en"
        label="{{ __('adminlte::adminlte.company_name_en') }}" rows="2" igroup-size="sm"
        wire:model.defer="name_en"
        placeholder="{{ __('adminlte::adminlte.company_name_en_placeholder') }}" />
    @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="name_ar"
        label="{{ __('adminlte::adminlte.company_name_ar') }}" rows="2" dir="rtl" igroup-size="sm"
        wire:model.defer="name_ar"
        placeholder="{{ __('adminlte::adminlte.company_name_ar_placeholder') }}" />
    @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-input name="email"
        label="{{ __('adminlte::adminlte.company_email') }}" type="email" igroup-size="sm"
        wire:model.defer="email"
        placeholder="{{ __('adminlte::adminlte.company_email_placeholder') }}" />
    @error('email') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-input name="phone"
        label="{{ __('adminlte::adminlte.company_phone') }}" type="text" igroup-size="sm"
        wire:model.defer="phone"
        placeholder="{{ __('adminlte::adminlte.company_phone_placeholder') }}" />
    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="address_en"
        label="{{ __('adminlte::adminlte.company_address_en') }}" rows="2" igroup-size="sm"
        wire:model.defer="address_en" />
    @error('address_en') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="address_ar"
        label="{{ __('adminlte::adminlte.company_address_ar') }}" rows="2" dir="rtl" igroup-size="sm"
        wire:model.defer="address_ar" />
    @error('address_ar') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-input name="location"
        label="{{ __('adminlte::adminlte.company_location') }}" type="text" igroup-size="sm"
        wire:model.defer="location"
        placeholder="{{ __('adminlte::adminlte.company_location_placeholder') }}" />
    @error('location') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="about_us_en"
        label="{{ __('adminlte::adminlte.about_us_en') }}" rows="3" igroup-size="sm"
        wire:model.defer="about_us_en" />
    @error('about_us_en') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="about_us_ar"
        label="{{ __('adminlte::adminlte.about_us_ar') }}" rows="3" dir="rtl" igroup-size="sm"
        wire:model.defer="about_us_ar" />
    @error('about_us_ar') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="mission_en"
        label="{{ __('adminlte::adminlte.mission_en') }}" rows="2" igroup-size="sm"
        wire:model.defer="mission_en" />
    @error('mission_en') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="mission_ar"
        label="{{ __('adminlte::adminlte.mission_ar') }}" rows="2" dir="rtl" igroup-size="sm"
        wire:model.defer="mission_ar" />
    @error('mission_ar') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="vision_en"
        label="{{ __('adminlte::adminlte.vision_en') }}" rows="2" igroup-size="sm"
        wire:model.defer="vision_en" />
    @error('vision_en') <small class="text-danger">{{ $message }}</small> @enderror

    <x-adminlte-textarea name="vision_ar"
        label="{{ __('adminlte::adminlte.vision_ar') }}" rows="2" dir="rtl" igroup-size="sm"
        wire:model.defer="vision_ar" />
    @error('vision_ar') <small class="text-danger">{{ $message }}</small> @enderror

    <div class="row">
        @php
            $colors = [
                ['name' => 'main_color', 'label' => __('adminlte::adminlte.main_color')],
                ['name' => 'sub_color', 'label' => __('adminlte::adminlte.sub_color')],
                ['name' => 'text_color', 'label' => __('adminlte::adminlte.text_color')],
                ['name' => 'button_color', 'label' => __('adminlte::adminlte.button_color')],
                ['name' => 'button_text_color', 'label' => __('adminlte::adminlte.button_text_color')],
                ['name' => 'icon_color', 'label' => __('adminlte::adminlte.icon_color')],
                ['name' => 'text_field_color', 'label' => __('adminlte::adminlte.text_field_color')],
                ['name' => 'card_color', 'label' => __('adminlte::adminlte.card_color')],
                ['name' => 'label_color', 'label' => __('adminlte::adminlte.label_color')],
                ['name' => 'hint_color', 'label' => __('adminlte::adminlte.hint_color')],
            ];
        @endphp

        @foreach ($colors as $c)
            <div class="col-sm-6 col-md-4 mb-3">
                <x-adminlte-input
                    name="{{ $c['name'] }}"
                    label="{{ $c['label'] }}"
                    type="color" igroup-size="sm"
                    wire:model.defer="{{ $c['name'] }}" />
                @error($c['name']) <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        @endforeach
    </div>

    <x-adminlte-button
        label="{{ __('adminlte::adminlte.save_information') }}"
        type="submit" theme="success"
        class="full-width-btn" icon="fas fa-save" />
</form>
