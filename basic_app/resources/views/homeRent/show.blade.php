{{-- resources/views/home_rent/_show.blade.php --}}
{{-- expects: $homeRent (model) --}}
@extends('adminlte::page')
@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            {{ __('adminlte::adminlte.home_rent_details') }}
        </h3>

        {{-- Status badge --}}
        @if($homeRent->is_active)
            <span class="badge badge-success">
                {{ __('adminlte::adminlte.active') }}
            </span>
        @else
            <span class="badge badge-secondary">
                {{ __('adminlte::adminlte.inactive') }}
            </span>
        @endif
    </div>

    <div class="card-body">
        <div class="row">
            {{-- Image --}}
            <div class="col-md-4 mb-3 text-center">
                @if(!empty($homeRent->image))
                    <img src="{{ asset('storage/'.$homeRent->image) }}"
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
                    <a href="{{ $homeRent->video }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-video mr-1"></i>
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

                    {{-- Description EN (optional – if you have it) --}}
                    @if(!empty($homeRent->description_en))
                        <dt class="col-sm-4">{{ __('adminlte::adminlte.description') }} EN</dt>
                        <dd class="col-sm-8">
                            {{ $homeRent->description_en }}
                        </dd>
                    @endif

                    {{-- Description AR --}}
                    @if(!empty($homeRent->description_ar))
                        <dt class="col-sm-4">{{ __('adminlte::adminlte.description') }} AR</dt>
                        <dd class="col-sm-8 text-right" dir="rtl">
                            {{ $homeRent->description_ar }}
                        </dd>
                    @endif

                    {{-- Price --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.rent_price') }}</dt>
                    <dd class="col-sm-8">
                        {{ number_format($homeRent->rent_price, 2) }}
                    </dd>

                    {{-- Bedrooms / Bathrooms --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.bedrooms') }}</dt>
                    <dd class="col-sm-8">{{ $homeRent->number_of_bedrooms }}</dd>

                    <dt class="col-sm-4">{{ __('adminlte::adminlte.bathrooms') }}</dt>
                    <dd class="col-sm-8">{{ $homeRent->number_of_bathrooms }}</dd>

                    {{-- Ratings --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.average_rating') }}</dt>
                    <dd class="col-sm-8">
                        {{ $homeRent->average_rating }}
                        @if($homeRent->total_ratings)
                            <small class="text-muted">
                                ({{ $homeRent->total_ratings }} {{ __('adminlte::adminlte.total_ratings') }})
                            </small>
                        @endif
                    </dd>

                    {{-- Location --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.location') }}</dt>
                    <dd class="col-sm-8">
                        {{ __('adminlte::adminlte.latitude') }}: {{ $homeRent->latitude }}<br>
                        {{ __('adminlte::adminlte.longitude') }}: {{ $homeRent->longitude }}
                    </dd>

                    {{-- Features (JSON) --}}
                    @if(!empty($homeRent->features))
                        @php
                            $features = is_array($homeRent->features)
                                ? $homeRent->features
                                : json_decode($homeRent->features, true) ?? [];
                        @endphp

                        <dt class="col-sm-4">{{ __('adminlte::adminlte.features') }}</dt>
                        <dd class="col-sm-8">
                            @if($features)
                                <ul class="list-unstyled mb-0">
                                    @foreach($features as $feature)
                                        <li>
                                            <i class="fas fa-check text-success mr-1"></i>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">
                                    {{ __('adminlte::adminlte.no_features') }}
                                </span>
                            @endif
                        </dd>
                    @endif

                    {{-- Meta --}}
                    <dt class="col-sm-4">{{ __('adminlte::adminlte.created_at') }}</dt>
                    <dd class="col-sm-8">
                        {{ optional($homeRent->created_at)->format('Y-m-d H:i') }}
                    </dd>

                    <dt class="col-sm-4">{{ __('adminlte::adminlte.updated_at') }}</dt>
                    <dd class="col-sm-8">
                        {{ optional($homeRent->updated_at)->format('Y-m-d H:i') }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
