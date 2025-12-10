@extends('adminlte::auth.auth-page')

@section('title', __('adminlte::adminlte.confirm_password'))

@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

@section('content')
<div class="auth-confirm-wrapper d-flex justify-content-center align-items-center">
    <div class="auth-confirm-card">

        {{-- Header --}}
        <div class="auth-confirm-header text-center mb-4">
            <img src="{{ asset('assets/Images/logo.png') }}"
                 alt="Logo"
                 class="auth-confirm-logo mb-3">

            <h2 class="auth-confirm-title">
                {{ __('adminlte::adminlte.comfirm_passwors') }}
            </h2>

            <p class="auth-confirm-subtitle">
                {{ __('adminlte::adminlte.please_confirm_continuing_before') }}
            </p>
        </div>

        {{-- Error --}}
        @if (session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="form-group mb-3">
                <label for="password" class="auth-confirm-label">
                    {{ __('adminlte::adminlte.confirm_password') }}
                </label>

                <div class="auth-input-wrapper">
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           autofocus
                           class="form-control auth-confirm-input @error('password') is-invalid @enderror">

                    <button type="button"
                            class="password-toggle-btn"
                            tabindex="-1"
                            aria-label="Toggle password visibility">
                        <i class="far fa-eye"></i>
                    </button>
                </div>

                @error('password')
                    <span class="invalid-feedback d-block">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn auth-confirm-btn w-100 mb-2">
                {{ __('adminlte::adminlte.confirm_password') }}
            </button>
        </form>

        <div class="text-center auth-confirm-footer-link">
            <a href="{{ route('password.request') }}">
                {{ __('adminlte::adminlte.forgot_password') }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    :root {
        --auth-confirm-bg: #f3f4ff;
        --auth-card-bg: #ffffff;
        --auth-accent: #6C63FF;
        --auth-text-main: #22223B;
        --auth-text-muted: #888888;
        --auth-border: #e0e0f1;
    }

    .auth-confirm-wrapper {
        min-height: 100vh;
        padding: 24px;
        background: radial-gradient(circle at top, #e0e7ff 0, #f9fafb 45%, #eef2ff 100%);
    }

    .auth-confirm-card {
        width: 100%;
        max-width: 420px;
        background: var(--auth-card-bg);
        border-radius: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        padding: 32px 28px 24px;
        position: relative;
        overflow: hidden;
    }

    .auth-confirm-card::before {
        content: "";
        position: absolute;
        top: -40px;
        {{ $isRtl ? 'right' : 'left' }}: -40px;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(108, 99, 255, .18), transparent 55%);
        opacity: .8;
        pointer-events: none;
    }

    .auth-confirm-logo {
        height: 52px;
        width: auto;
    }

    .auth-confirm-title {
        font-size: 1.7rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--auth-text-main);
    }

    .auth-confirm-subtitle {
        margin: 0;
        font-size: .95rem;
        color: var(--auth-text-muted);
    }

    .auth-confirm-label {
        font-weight: 600;
        font-size: .95rem;
        color: var(--auth-text-main);
        margin-bottom: 6px;
        display: block;
    }

    .auth-input-wrapper {
        position: relative;
    }

    .auth-confirm-input {
        width: 100%;
        padding: 12px 44px 12px 14px;
        border-radius: 12px;
        border: 1.5px solid var(--auth-border);
        background: #f8f8ff;
        font-size: .98rem;
        transition: all .18s ease;
    }

    html[dir="rtl"] .auth-confirm-input {
        padding: 12px 14px 12px 44px;
    }

    .auth-confirm-input:focus {
        outline: none;
        border-color: var(--auth-accent);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.18);
        background: #ffffff;
    }

    .password-toggle-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        {{ $isRtl ? 'left' : 'right' }}: 10px;
        border: none;
        background: transparent;
        padding: 0;
        margin: 0;
        cursor: pointer;
        color: #9ca3af;
        font-size: 1rem;
    }

    .password-toggle-btn:focus {
        outline: none;
    }

    .auth-confirm-btn {
        background: var(--auth-accent);
        border-radius: 999px;
        border: none;
        padding: 12px 0;
        font-size: 1.02rem;
        font-weight: 600;
        color: #ffffff;
        box-shadow: 0 10px 25px rgba(108, 99, 255, 0.28);
        transition: background .18s ease, transform .1s ease, box-shadow .18s ease;
    }

    .auth-confirm-btn:hover {
        background: #5850ec;
        transform: translateY(-1px);
        box-shadow: 0 14px 30px rgba(88, 80, 236, 0.32);
    }

    .auth-confirm-btn:active {
        transform: translateY(0);
        box-shadow: 0 6px 18px rgba(88, 80, 236, 0.25);
    }

    .auth-confirm-footer-link {
        font-size: .95rem;
        color: var(--auth-text-muted);
    }

    .auth-confirm-footer-link a {
        color: var(--auth-accent);
        font-weight: 600;
        text-decoration: none;
    }

    .auth-confirm-footer-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 576px) {
        .auth-confirm-card {
            padding: 28px 20px 20px;
        }
        .auth-confirm-title {
            font-size: 1.45rem;
        }
    }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleBtn = document.querySelector('.password-toggle-btn');
        var input = document.getElementById('password');

        if (toggleBtn && input) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');

                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
        }
    });
</script>
@endpush
