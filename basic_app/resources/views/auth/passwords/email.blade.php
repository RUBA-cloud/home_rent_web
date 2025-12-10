{{-- resources/views/auth/passwords/email.blade.php --}}
@extends('adminlte::page')

@php
    use App\Helpers\CustomSettings;

    // Get app settings from CompanyInfo
    $settings = CustomSettings::appSettings();

    // Map to brand palette for this page
    $brand = [
        'main_color' => $settings['button_color'] ?? $settings['main_color'] ?? '#2563EB', // primary / button
        'sub_color'  => $settings['sub_color']     ?? '#0F172A',                           // card header / accent
        'bg_soft'    => $settings['card_color']    ?? '#F3F4F6',                           // page background
        'text_color' => $settings['text_color']    ?? '#111827',                           // main text
    ];

    // Optional: app name + logo from CompanyInfo
    $appName = $settings['name_en'] ?? config('app.name', 'My App');
    $logoUrl = $settings['image'] ? asset($settings['image']) : null;
@endphp

@section('title', 'Reset your password')

@section('content')
<div class="container py-5" style="background: {{ $brand['bg_soft'] }};">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">

            {{-- Success message when email is sent --}}
            @if (session('status'))
                <div class="alert alert-success shadow-sm mb-4">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-4">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-lg border-0" style="border-radius: 18px; overflow: hidden;">
                {{-- Header --}}
                <div class="card-header border-0 text-white"
                     style="background: radial-gradient(circle at top left, {{ $brand['main_color'] }}, {{ $brand['sub_color'] }});">
                    <div class="d-flex align-items-center">
                        @if($logoUrl)
                            <div class="me-3">
                                <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="height:40px; width:auto;">
                            </div>
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 42px; height: 42px; background: rgba(15,23,42,.25);">
                                <i class="fas fa-unlock-alt"></i>
                            </div>
                        @endif

                        <div>
                            <h5 class="mb-0">Forgot your password?</h5>
                            <small class="text-white-50">We’ll send a reset link to your email.</small>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body" style="color: {{ $brand['text_color'] }};">
                    <p class="mb-3" style="line-height: 1.6;">
                        Enter the email address associated with your account, and we’ll send you a link
                        to create a new password.
                    </p>

                    <form method="POST" action="{{ route('password.email') }}" autocomplete="off">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Email address
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input id="email"
                                       type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="you@example.com">
                            </div>

                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit"
                                class="btn w-100 mt-2"
                                style="
                                    background: linear-gradient(135deg, {{ $brand['main_color'] }}, {{ $brand['sub_color'] }});
                                    border-color: transparent;
                                    color: #ffffff;
                                    font-weight: 600;
                                    border-radius: 999px;
                                    padding: 0.6rem 1rem;">
                            <i class="fas fa-paper-plane me-1"></i>
                            Send password reset link
                        </button>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="card-footer bg-white border-0 text-center small text-muted">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <span class="mb-2 mb-md-0">
                            Remember your password?
                        </span>
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tiny brand note --}}
            <div class="text-center text-muted small mt-3">
                If you didn’t request a password reset, you can safely ignore this page.
            </div>
        </div>
    </div>
</div>
@endsection
