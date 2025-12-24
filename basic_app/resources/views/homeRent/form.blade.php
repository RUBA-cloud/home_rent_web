{{-- resources/views/home_rent/_form.blade.php --}}
@section('plugins.Select2', true)
@section('plugins.BsCustomFileInput', true)

@php
    $homeObj    = $homeRent ?? null;
    $httpMethod = strtoupper($method ?? 'POST');
    $isAr       = app()->getLocale() === 'ar';

    $broadcast = $broadcast ?? [
        'channel' => 'home_rent',
        'events'  => ['home_rent_updated'],
    ];

    $selectedCategoryId = old('category_id', data_get($homeObj,'category_id'));

    $oldFeatures = collect(old(
        'home_rent_features',
        data_get($homeObj,'features', collect())->pluck('id')->toArray()
    ));

    // payment period: daily | monthly
    $selectedPaymentWay = old('payment_way', data_get($homeObj,'payment_way','monthly'));

    // payment status: 0 unpaid | 1 paid | 2 pending
    $selectedPaymentStatus = (int) old('payment_status', data_get($homeObj,'payment_status', 0));

    $isCreate   = $httpMethod === 'POST';
    $activeFlag = (int) old('is_available', (int) data_get($homeObj,'is_available', 1));

    // ✅ map default center (Amman)
    $defaultLat = 31.9539;
    $defaultLng = 35.9106;

    // ✅ current saved values
    $initLat = old('latitude',  data_get($homeObj,'latitude'));
    $initLng = old('longitude', data_get($homeObj,'longitude'));
@endphp

<form id="home-rent-form"
      dir="{{ $isAr ? 'rtl' : 'ltr' }}"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'>
    @csrf
    @unless(in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($homeObj?->id))
        <input type="hidden" name="id" value="{{ $homeObj->id }}">
    @endif

    {{-- Global validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle {{ $isAr ? 'ml-2' : 'mr-2' }}"></i>
                <strong>
                    {{ __('adminlte::adminlte.validation_error') ?? ($isAr ? 'يوجد بعض الأخطاء في الإدخال' : 'There are some validation errors') }}
                </strong>
            </div>
            <ul class="mb-0 mt-2 {{ $isAr ? 'pr-3' : 'pl-3' }}">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TOP CARD --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center {{ $isAr ? 'ml-3' : 'mr-3' }}"
                     style="width: 40px; height: 40px;">
                    <i class="fas fa-home"></i>
                </div>
                <div>
                    <h5 class="mb-1 font-weight-bold">
                        @if($isCreate)
                            {{ $isAr ? 'إضافة عقار للإيجار' : 'Create Home Rent' }}
                        @else
                            {{ $isAr ? 'تعديل بيانات العقار' : 'Edit Home Rent' }}
                        @endif
                    </h5>
                    <small class="text-muted">
                        {{ $isAr ? 'املأ بيانات العقار ثم احفظ التغييرات' : 'Fill in the home details then save your changes.' }}
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center">
                @if($homeObj?->id)
                    <span class="badge badge-light border {{ $isAr ? 'ml-2' : 'mr-2' }}">
                        <i class="fas fa-hashtag {{ $isAr ? 'ml-1' : 'mr-1' }}"></i> {{ $homeObj->id }}
                    </span>
                @endif

                <span class="badge badge-{{ $activeFlag ? 'success' : 'secondary' }}">
                    <i class="fas fa-toggle-on {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                    {{ $activeFlag ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-7">

            {{-- Basic information --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-info-circle text-primary {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $isAr ? 'المعلومات الأساسية' : 'Basic Information' }}
                    </h6>
                    <small class="text-muted">
                        {{ $isAr ? 'اسم العقار ووصفه وعنوانه باللغتين' : 'Home name, description and address in both languages.' }}
                    </small>
                </div>
                <div class="card-body">
                    {{-- Name EN --}}
                    <div class="form-group">
                        <label for="name_en" class="font-weight-semibold">
                            {{ __('adminlte::adminlte.name_en') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name_en" id="name_en"
                               class="form-control @error('name_en') is-invalid @enderror"
                               value="{{ old('name_en', data_get($homeObj,'name_en','')) }}" required>
                        @error('name_en') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- Name AR --}}
                    <div class="form-group">
                        <label for="name_ar" class="font-weight-semibold">
                            {{ __('adminlte::adminlte.name_ar') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name_ar" id="name_ar"
                               class="form-control @error('name_ar') is-invalid @enderror"
                               value="{{ old('name_ar', data_get($homeObj,'name_ar','')) }}" required>
                        @error('name_ar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="description_en" class="font-weight-semibold">
                                {{ __('adminlte::adminlte.description') }} (EN)
                            </label>
                            <textarea name="description_en" id="description_en" rows="3"
                                      class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', data_get($homeObj,'description_en','')) }}</textarea>
                            @error('description_en') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="description_ar" class="font-weight-semibold">
                                {{ __('adminlte::adminlte.description') }} (AR)
                            </label>
                            <textarea name="description_ar" id="description_ar" rows="3"
                                      class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', data_get($homeObj,'description_ar','')) }}</textarea>
                            @error('description_ar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="address_en" class="font-weight-semibold">
                                {{ __('adminlte::adminlte.address') }} (EN)
                            </label>
                            <textarea name="address_en" id="address_en" rows="2"
                                      class="form-control @error('address_en') is-invalid @enderror">{{ old('address_en', data_get($homeObj,'address_en','')) }}</textarea>
                            @error('address_en') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="address_ar" class="font-weight-semibold">
                                {{ __('adminlte::adminlte.address') }} (AR)
                            </label>
                            <textarea name="address_ar" id="address_ar" rows="2"
                                      class="form-control @error('address_ar') is-invalid @enderror">{{ old('address_ar', data_get($homeObj,'address_ar','')) }}</textarea>
                            @error('address_ar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location & details (no lat/lng inputs here anymore) --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-map-marker-alt text-primary {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $isAr ? 'التفاصيل' : 'Details' }}
                    </h6>
                    <small class="text-muted">
                        {{ $isAr ? 'عدد الغرف والحمامات والمساحة' : 'Bedrooms, bathrooms and size.' }}
                    </small>
                </div>

                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="number_of_bedrooms" class="font-weight-semibold">{{ __('adminlte::adminlte.bedrooms') }}</label>
                            <input type="number" name="number_of_bedrooms" id="number_of_bedrooms"
                                   class="form-control @error('number_of_bedrooms') is-invalid @enderror"
                                   value="{{ old('number_of_bedrooms', data_get($homeObj,'number_of_bedrooms','')) }}">
                            @error('number_of_bedrooms') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="number_of_bathrooms" class="font-weight-semibold">{{ __('adminlte::adminlte.bathrooms') }}</label>
                            <input type="number" name="number_of_bathrooms" id="number_of_bathrooms"
                                   class="form-control @error('number_of_bathrooms') is-invalid @enderror"
                                   value="{{ old('number_of_bathrooms', data_get($homeObj,'number_of_bathrooms','')) }}">
                            @error('number_of_bathrooms') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="size" class="font-weight-semibold">{{ __('adminlte::adminlte.size') }}</label>
                            <input type="text" name="size" id="size"
                                   class="form-control @error('size') is-invalid @enderror"
                                   value="{{ old('size', data_get($homeObj,'size','')) }}">
                            @error('size') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Features & Active --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-sliders-h text-primary {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $isAr ? 'المميزات والحالة' : 'Features & Status' }}
                    </h6>
                    <small class="text-muted">
                        {{ $isAr ? 'اختاري مميزات العقار وحالة التفعيل' : 'Select home features and active status.' }}
                    </small>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label for="features_select" class="font-weight-semibold">
                            {{ __('adminlte::adminlte.home_rent_feature') }}
                        </label>

                        <select name="home_rent_features[]"
                                id="features_select"
                                class="form-control custom-select2
                                       @error('home_rent_features') is-invalid @enderror
                                       @error('home_rent_features.*') is-invalid @enderror"
                                multiple>
                            @forelse($homeFeatures as $feature)
                                <option value="{{ $feature->id }}" {{ $oldFeatures->contains($feature->id) ? 'selected' : '' }}>
                                    {{ $isAr ? ($feature->name_ar ?? $feature->name_en) : ($feature->name_en ?? $feature->name_ar) }}
                                </option>
                            @empty
                                <option value="" disabled>{{ __('adminlte::adminlte.no_records') }}</option>
                            @endforelse
                        </select>

                        <small class="form-text text-muted">
                            {{ $isAr ? 'يمكنك اختيار أكثر من ميزة.' : 'You can select multiple features.' }}
                        </small>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_available" value="0">
                            <input type="checkbox" name="is_available" class="custom-control-input" id="is_available"
                                   value="1" {{ (int)$activeFlag ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_available">
                                <i class="fas fa-bolt {{ $isAr ? 'ml-1' : 'mr-1' }} text-warning"></i>
                                {{ __('adminlte::adminlte.is_available') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PRICING --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-tags text-primary {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $isAr ? 'التسعير والتصنيف' : 'Pricing & Classification' }}
                    </h6>
                    <small class="text-muted">
                        {{ $isAr ? 'حدد الأسعار وفترة الدفع وحالة الدفع والتصنيف' : 'Set prices, payment period/status and category.' }}
                    </small>
                </div>

                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="rent_price" class="font-weight-semibold">{{ __('adminlte::adminlte.rent_price') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="rent_price" id="rent_price"
                                       class="form-control @error('rent_price') is-invalid @enderror"
                                       value="{{ old('rent_price', data_get($homeObj,'rent_price','')) }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                </div>
                            </div>
                            @error('rent_price') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="price" class="font-weight-semibold">{{ __('adminlte::adminlte.price') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="price" id="price"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price', data_get($homeObj,'price','')) }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-coins"></i></span>
                                </div>
                            </div>
                            @error('price') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Payment Period --}}
                    <div class="form-group">
                        <label class="font-weight-semibold d-flex align-items-center justify-content-between">
                            <span>{{ $isAr ? 'فترة الدفع' : 'Payment Period' }} <span class="text-danger">*</span></span>
                            <span class="badge badge-light border" id="periodBadge">
                                <i class="fas fa-calendar-alt {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                                <span id="periodBadgeText">
                                    {{ $selectedPaymentWay === 'daily' ? ($isAr ? 'يومي' : 'Daily') : ($isAr ? 'شهري' : 'Monthly') }}
                                </span>
                            </span>
                        </label>

                        <select id="payment_way"
                                name="payment_way"
                                class="d-none @error('payment_way') is-invalid @enderror"
                                required>
                            <option value="daily"   {{ $selectedPaymentWay === 'daily' ? 'selected' : '' }}>{{ $isAr ? 'يومي' : 'Daily' }}</option>
                            <option value="monthly" {{ $selectedPaymentWay === 'monthly' ? 'selected' : '' }}>{{ $isAr ? 'شهري' : 'Monthly' }}</option>
                        </select>

                        <div class="d-flex">
                            <button type="button"
                                    class="btn btn-outline-primary flex-fill {{ $isAr ? 'ml-2' : 'mr-2' }} pay-card-btn"
                                    data-value="daily">
                                <i class="fas fa-sun {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                                {{ $isAr ? 'يومي' : 'Daily' }}
                            </button>

                            <button type="button"
                                    class="btn btn-outline-primary flex-fill pay-card-btn"
                                    data-value="monthly">
                                <i class="fas fa-calendar {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                                {{ $isAr ? 'شهري' : 'Monthly' }}
                            </button>
                        </div>

                        @error('payment_way')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Payment Status + Category --}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="payment_status" class="font-weight-semibold">
                                {{ $isAr ? 'حالة الدفع' : 'Payment Status' }} <span class="text-danger">*</span>
                            </label>

                            <select name="payment_status"
                                    id="payment_status"
                                    class="form-control custom-select2 @error('payment_status') is-invalid @enderror"
                                    required>
                                <option value="0" {{ $selectedPaymentStatus === 0 ? 'selected' : '' }}>
                                    {{ $isAr ? 'غير مدفوع' : 'Unpaid' }}
                                </option>
                                <option value="2" {{ $selectedPaymentStatus === 2 ? 'selected' : '' }}>
                                    {{ $isAr ? 'قيد المعالجة' : 'Pending' }}
                                </option>
                                <option value="1" {{ $selectedPaymentStatus === 1 ? 'selected' : '' }}>
                                    {{ $isAr ? 'مدفوع' : 'Paid' }}
                                </option>
                            </select>

                            @error('payment_status')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mb-0">
                            <label for="category_id" class="font-weight-semibold">
                                {{ __('adminlte::adminlte.category') }} <span class="text-danger">*</span>
                            </label>

                            <select name="category_id"
                                    id="category_id"
                                    class="form-control custom-select2 @error('category_id') is-invalid @enderror"
                                    required>
                                <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ (string)old('category_id', data_get($homeObj,'category_id')) === (string)$category->id ? 'selected' : '' }}>
                                        {{ $isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('category_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Media only --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-photo-video text-primary {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $isAr ? 'الوسائط' : 'Media' }}
                    </h6>
                    <small class="text-muted">
                        {{ $isAr ? 'ارفع صورة وفيديو للعقار' : 'Upload image and video for the home.' }}
                    </small>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-semibold d-block">{{ __('adminlte::adminlte.image') }}</label>
                        <x-upload-image :image="$homeObj->image ?? null" label="" name="image" id="image" />
                    </div>

                    <div class="form-group mb-0">
                        <label for="video" class="font-weight-semibold">{{ __('adminlte::adminlte.video') }}</label>
                        <div class="custom-file">
                            <input type="file" name="video" id="video"
                                   class="custom-file-input @error('video') is-invalid @enderror"
                                   accept="video/mp4,video/webm,video/ogg">
                            <label class="custom-file-label" for="video"></label>
                            @error('video') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                        <small class="form-text text-muted">
                            {{ $isAr ? 'يدعم MP4 / WEBM / OGG' : 'Supports MP4 / WEBM / OGG' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ MAP SECTION (above save button) --}}
    <div class="card shadow-sm border-0 mt-2 mb-3">
        <div class="card-header bg-white border-0 pb-0">
            <h6 class="mb-0 font-weight-bold">
                <i class="fas fa-map-marker-alt text-primary {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                {{ $isAr ? 'تحديد الموقع على الخريطة' : 'Pick Location on Map' }}
            </h6>
            <small class="text-muted">
                {{ $isAr ? 'اضغطي على الخريطة أو اسحبي العلامة لاختيار الموقع.' : 'Click on map or drag marker to select location.' }}
            </small>
        </div>

        <div class="card-body">
            {{-- ✅ Hidden inputs to SAVE in DB --}}
            <input type="hidden" name="latitude" id="latitude"  value="{{ old('latitude', data_get($homeObj,'latitude','')) }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', data_get($homeObj,'longitude','')) }}">

            <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                <div class="small text-muted">
                    {{ $isAr ? 'اختاري الموقع وسيتم حفظ الإحداثيات تلقائياً.' : 'Pick a location and coordinates will be saved automatically.' }}
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-primary" id="btnMyLocation">
                        <i class="fas fa-location-arrow {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $isAr ? 'موقعي الحالي' : 'My Location' }}
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearLocation">
                        <i class="fas fa-times {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $isAr ? 'مسح' : 'Clear' }}
                    </button>
                </div>
            </div>

            <div id="homeRentMap" style="height: 320px; border-radius: 12px; overflow:hidden;"></div>

            {{-- ✅ show values DOWN --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="p-2 border rounded bg-light">
                        <div class="small text-muted mb-1">Latitude</div>
                        <div class="font-weight-bold" id="latText">-</div>
                    </div>
                </div>
                <div class="col-md-6 mt-2 mt-md-0">
                    <div class="p-2 border rounded bg-light">
                        <div class="small text-muted mb-1">Longitude</div>
                        <div class="font-weight-bold" id="lngText">-</div>
                    </div>
                </div>
            </div>

            @error('latitude')  <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            @error('longitude') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- SUBMIT BUTTON --}}
    <div class="card shadow-sm border-0 mt-2">
        <div class="card-body">
            <x-adminlte-button
                label="{{ $isCreate ? __('adminlte::adminlte.save_information') : __('adminlte::adminlte.update_information') }}"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
        </div>
    </div>
</form>

{{-- Hidden anchor for broadcasting --}}
<div id="home-rent-form-listener"
     data-channel="{{ $broadcast['channel'] }}"
     data-events='@json($broadcast["events"])'>
</div>

@push('js')
<script>
$(document).ready(function () {
  const isRtl = @json($isAr);

  if (window.bsCustomFileInput) {
      window.bsCustomFileInput.init();
  }

  // Select2 init
  $('.custom-select2').select2({
    theme: 'bootstrap4',
    width: '100%',
    dir: isRtl ? 'rtl' : 'ltr',
    allowClear: true,
    placeholder: @json(__('adminlte::adminlte.select')),
  });

  // Payment Period buttons
  const $paymentSelect = $('#payment_way');
  const $badgeText = $('#periodBadgeText');

  function applyPaymentWay(value){
    $paymentSelect.val(value);
    $('.pay-card-btn').removeClass('active');
    $('.pay-card-btn[data-value="'+value+'"]').addClass('active');
    $badgeText.text(value === 'daily' ? (isRtl ? 'يومي' : 'Daily') : (isRtl ? 'شهري' : 'Monthly'));
  }

  $('.pay-card-btn').on('click', function(){
    applyPaymentWay($(this).data('value'));
  });

  applyPaymentWay(@json($selectedPaymentWay));

  // Broadcasting
  const anchor = document.getElementById('home-rent-form-listener');
  if (anchor) {
    const channelName = anchor.dataset.channel || 'home_rent';
    let events = [];
    try { events = JSON.parse(anchor.dataset.events || '["home_rent_updated"]'); }
    catch { events = ['home_rent_updated']; }

    events.forEach(eventName => {
      const handler = function() {
        if (window.toastr) toastr.info(@json(__('adminlte::adminlte.saved_successfully')));
      };

      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, eventName, handler);
      } else {
        window.__pageBroadcasts = window.__pageBroadcasts || [];
        window.__pageBroadcasts.push({channel: channelName, event: eventName, handler});
      }
    });
  }
});
</script>

<script>
  const HOME_RENT_IS_AR = @json($isAr);
  const HOME_RENT_DEFAULT_CENTER = { lat: Number(@json($defaultLat)), lng: Number(@json($defaultLng)) };
  const HOME_RENT_INIT_LAT = @json($initLat);
  const HOME_RENT_INIT_LNG = @json($initLng);

  let homeRentMap, homeRentMarker;

  function hr_t(ar, en){ return HOME_RENT_IS_AR ? ar : en; }
  function hr_parseNum(v){ const n = parseFloat(v); return Number.isFinite(n) ? n : null; }

  function hr_setTexts(lat, lng){
    document.getElementById('latText').textContent = (lat==null) ? '-' : lat.toFixed(6);
    document.getElementById('lngText').textContent = (lng==null) ? '-' : lng.toFixed(6);
  }

  function hr_setInputs(lat, lng){
    document.getElementById('latitude').value  = (lat ?? '');
    document.getElementById('longitude').value = (lng ?? '');
  }

  function hr_ensureMarker(pos){
    if (!homeRentMarker){
      homeRentMarker = new google.maps.Marker({
        map: homeRentMap,
        position: pos,
        draggable: true
      });

      homeRentMarker.addListener('dragend', (e) => {
        const lat = e.latLng.lat();
        const lng = e.latLng.lng();
        hr_setInputs(lat, lng);
        hr_setTexts(lat, lng);
      });
    } else {
      homeRentMarker.setPosition(pos);
      homeRentMarker.setMap(homeRentMap);
    }
  }

  function hr_setLocation(lat, lng, pan = true){
    hr_setInputs(lat, lng);
    hr_setTexts(lat, lng);
    const pos = { lat: Number(lat), lng: Number(lng) };
    hr_ensureMarker(pos);
    if (pan){ homeRentMap.panTo(pos); homeRentMap.setZoom(15); }
  }

  function hr_clearLocation(){
    hr_setInputs(null, null);
    hr_setTexts(null, null);
    if (homeRentMarker){ homeRentMarker.setMap(null); homeRentMarker = null; }
    homeRentMap.panTo(HOME_RENT_DEFAULT_CENTER);
    homeRentMap.setZoom(12);
  }

  window.initHomeRentMap = function () {
    const lat0 = hr_parseNum(HOME_RENT_INIT_LAT);
    const lng0 = hr_parseNum(HOME_RENT_INIT_LNG);
    const hasInit = (lat0 != null && lng0 != null);

    homeRentMap = new google.maps.Map(document.getElementById('homeRentMap'), {
      center: hasInit ? {lat: lat0, lng: lng0} : HOME_RENT_DEFAULT_CENTER,
      zoom: hasInit ? 15 : 12,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: true,
    });

    if (hasInit) hr_setLocation(lat0, lng0, false);
    else hr_setTexts(null, null);

    homeRentMap.addListener('click', (e) => {
      hr_setLocation(e.latLng.lat(), e.latLng.lng(), false);
    });

    document.getElementById('btnMyLocation')?.addEventListener('click', () => {
      if (!navigator.geolocation) {
        alert(hr_t('المتصفح لا يدعم تحديد الموقع.', 'Geolocation is not supported by your browser.'));
        return;
      }
      navigator.geolocation.getCurrentPosition(
        (pos) => hr_setLocation(pos.coords.latitude, pos.coords.longitude, true),
        () => alert(hr_t('فعّلي صلاحية الموقع من المتصفح.', 'Please allow location permission in your browser.')),
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
      );
    });

    document.getElementById('btnClearLocation')?.addEventListener('click', hr_clearLocation);
  };
</script>

<script
  src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initHomeRentMap"
  async defer></script>
@endpush
