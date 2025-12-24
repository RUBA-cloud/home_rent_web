{{-- resources/views/home_rent/_show.blade.php --}}
{{-- expects: $homeRent (model) --}}
@extends('adminlte::page')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';

    // Payment way labels
    $paymentWay = data_get($homeRent, 'payment_way', null);
    $paymentWayLabel = match($paymentWay) {
        'daily'   => $isAr ? 'يومي' : 'Daily',
        'monthly' => $isAr ? 'شهري' : 'Monthly',
        default   => $isAr ? 'غير محدد' : 'Not set',
    };

    // Payment status labels
    $paymentStatus = (int) data_get($homeRent, 'payment_status', 0);
    $paymentStatusLabel = match($paymentStatus) {
        1       => $isAr ? 'مدفوع' : 'Paid',
        2       => $isAr ? 'قيد المعالجة' : 'Pending',
        default => $isAr ? 'غير مدفوع' : 'Unpaid',
    };
    $paymentStatusBadge = match($paymentStatus) {
        1       => 'success',
        2       => 'warning',
        default => 'secondary',
    };

    // Availability
    $isAvailable = (int) data_get($homeRent, 'is_available', 0);



@endphp

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            {{ __('adminlte::adminlte.home_rent_details') }}
        </h3>

        <div class="d-flex align-items-center">
            {{-- Active badge --}}
            @if((int) data_get($homeRent,'is_active',0) === 1)
                <span class="badge badge-success {{ $isAr ? 'ml-2' : 'mr-2' }}">
                    {{ __('adminlte::adminlte.active') }}
                </span>
            @else
                <span class="badge badge-secondary {{ $isAr ? 'ml-2' : 'mr-2' }}">
                    {{ __('adminlte::adminlte.inactive') }}
                </span>
            @endif

            {{-- Payment status badge --}}
            <span class="badge badge-{{ $paymentStatusBadge }}">
                <i class="fas fa-credit-card {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                {{ $paymentStatusLabel }}
            </span>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            {{-- Image --}}
            <div class="col-md-4 mb-3 text-center">
                @if(!empty($homeRent->image))
                    <img src="{{ asset($homeRent->image) }}"
                         alt="{{ $homeRent->name_en }}"
                         class="img-fluid rounded shadow-sm mb-2">
                @else
                    <div class="border rounded d-flex align-items-center justify-content-center"
                         style="height: 180px;">
                        <span class="text-muted">
                            {{ __('adminlte::adminlte.no_image') }}
                        </span>
                    </div>
                @endif

                {{-- Optional: video link --}}
                @if(!empty($homeRent->video))
                    <a href="{{ str_starts_with($homeRent->video,'http') ? $homeRent->video : asset('storage/'.$homeRent->video) }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-video {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                        {{ __('adminlte::adminlte.view_video') }}
                    </a>
                @endif
            </div>

            {{-- Main info --}}
            <div class="col-md-8">
                <dl class="row mb-0">

                    {{-- Name EN --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.name_en') }}</dt>
                    <dd class="col-sm-8">{{ $homeRent->name_en }}</dd>

                    {{-- Name AR --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.name_ar') }}</dt>
                    <dd class="col-sm-8 text-right" dir="rtl">
                        {{ $homeRent->name_ar }}
                    </dd>

                    {{-- Address EN --}}
                    @if(!empty($homeRent->address_en))
                        <dt class="col-sm-4">{{ __('adminlte::adminlte.address') }} (EN)</dt>
                        <dd class="col-sm-8">{{ $homeRent->address_en }}</dd>
                    @endif

                    {{-- Address AR --}}
                    @if(!empty($homeRent->address_ar))
                        <dt class="col-sm-4">{{ __('adminlte::adminlte.address') }} (AR)</dt>
                        <dd class="col-sm-8 text-right" dir="rtl">{{ $homeRent->address_ar }}</dd>
                    @endif

                    {{-- Description EN --}}
                    @if(!empty($homeRent->description_en))
                        <dt class="col-sm-4">{{ __('adminlte::adminlte.description') }} (EN)</dt>
                        <dd class="col-sm-8">{{ $homeRent->description_en }}</dd>
                    @endif

                    {{-- Description AR --}}
                    @if(!empty($homeRent->description_ar))
                        <dt class="col-sm-4">{{ __('adminlte::adminlte.description') }} (AR)</dt>
                        <dd class="col-sm-8 text-right" dir="rtl">{{ $homeRent->description_ar }}</dd>
                    @endif

                    {{-- Pricing --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.rent_price') }}</dt>
                    <dd class="col-sm-8">
                        {{ is_null($homeRent->rent_price) ? '-' : number_format((float)$homeRent->rent_price, 2) }}
                    </dd>

                    <dt class="col-sm-4">{{ __('adminlte::adminlte.price') }}</dt>
                    <dd class="col-sm-8">
                        {{ is_null($homeRent->price) ? '-' : number_format((float)$homeRent->price, 2) }}
                    </dd>

                    {{-- Payment way --}}
                    <dt class="col-sm-4">{{ $isAr ? 'طريقة/فترة الدفع' : 'Payment Period' }}</dt>
                    <dd class="col-sm-8">
                        <span class="badge badge-light border">
                            <i class="fas fa-calendar-alt {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                            {{ $paymentWayLabel }}
                        </span>
                    </dd>

                    {{-- Payment status --}}
                    <dt class="col-sm-4">{{ $isAr ? 'حالة الدفع' : 'Payment Status' }}</dt>
                    <dd class="col-sm-8">
                        <span class="badge badge-{{ $paymentStatusBadge }}">
                            {{ $paymentStatusLabel }}
                        </span>
                    </dd>

                    {{-- Is Available --}}
                    <dt class="col-sm-4">{{ $isAr ? 'متاح' : 'Available' }}</dt>
                    <dd class="col-sm-8">
                        @if($isAvailable)
                            <span class="badge badge-success">
                                <i class="fas fa-check {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                                {{ $isAr ? 'نعم' : 'Yes' }}
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-times {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                                {{ $isAr ? 'لا' : 'No' }}
                            </span>
                        @endif
                    </dd>

                    {{-- Bedrooms / Bathrooms --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.bedrooms') }}</dt>
                    <dd class="col-sm-8">{{ $homeRent->number_of_bedrooms ?? '-' }}</dd>

                    <dt class="col-sm-4">{{ __('adminlte::adminlte.bathrooms') }}</dt>
                    <dd class="col-sm-8">{{ $homeRent->number_of_bathrooms ?? '-' }}</dd>

                    {{-- Ratings --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.average_rating') }}</dt>
                    <dd class="col-sm-8">
                        {{ $homeRent->average_rating ?? 0 }}
                        @if(!empty($homeRent->total_ratings))
                            <small class="text-muted">
                                ({{ $homeRent->total_ratings }} {{ __('adminlte::adminlte.total_ratings') }})
                            </small>
                        @endif
                    </dd>

                    {{-- Location --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.location') }}</dt>
                    <dd class="col-sm-8">
                        {{ __('adminlte::adminlte.latitude') }}: {{ $homeRent->latitude ?? '-' }}<br>
                        {{ __('adminlte::adminlte.longitude') }}: {{ $homeRent->longitude ?? '-' }}
                    </dd>
                    {{-- Features --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.features') }}</dt>
                    <dd class="col-sm-8">
                        @if(!empty($homeRent->homeFeatures))
                            <div class="d-flex flex-wrap">
                                @foreach($homeRent->homeFeatures as $f)
                                    <span class="badge badge-light border m-1">
                                        <i class="fas fa-check text-success {{ $isAr ? 'ml-1' : 'mr-1' }}"></i>
                                   {{    $isAr?$f->name_ar:  $f->name_en }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">{{ __('adminlte::adminlte.no_features') }}</span>
                        @endif
                    </dd>

                    {{-- Meta --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.created_at') }}</dt>
                    <dd class="col-sm-8">{{ optional($homeRent->created_at)->format('Y-m-d H:i') }}</dd>

                    <dt class="col-sm-4">{{ __('adminlte::adminlte.updated_at') }}</dt>
                    <dd class="col-sm-8">{{ optional($homeRent->updated_at)->format('Y-m-d H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
