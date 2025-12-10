@extends('adminlte::auth.auth-page')
@section('title', __('adminlite::register'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Left illustration --}}
    <div style="flex:1.1; display:flex; align-items:center; justify-content:center; background:transparent;">
        <img src="{{ asset('assets/Images/coffebeans.jpg') }}" alt="Illustration"
            style="height:100%; width:auto; max-width:100%; object-fit:cover;">
    </div>

    {{-- Right register form --}}
    <div style="flex:1.3; background:#fff; border-radius: 0 24px 24px 0; display:flex; flex-direction:column; justify-content:center; align-items:center; min-width:320px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.12);">
        <div style="width:100%; max-width:420px; margin:0 auto; padding:48px 32px;">

            <div style="display:flex; justify-content:flex-end; margin-bottom:18px;">
                <span style="color:#888; font-size:0.97rem; margin-right:8px;">{{ __('adminlte::adminlte.already_have_account') }}</span>
                <a href="{{ route('login') }}" style="color:#6C63FF; font-weight:600; border:1px solid #eee; border-radius:8px; padding:4px 18px; background:#fff; text-decoration:none;">{{ __('adminlte::adminlte.login') }}</a>
            </div>

            <div style="text-align:left; margin-bottom:32px;">
                <h2 style="font-size:2rem; font-weight:700; color:#22223B; margin-bottom:8px;">{{ __('adminlte::adminlte.register')}} </h2>
                <p style="color:#888; font-size:1rem; margin-bottom:0;">{{ __('adminlte::adminlte.send_password_reset_link') }}</p>
            </div>

            @if(session('message'))
                <div class="alert alert-info" role="alert" style="margin-bottom: 18px;">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div style="margin-bottom: 18px;">
                    <label for="name" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;">{{ __('adminlte::adminlte.full_name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
                    @error('name')
                        <span style="color:#e3342f; font-size:0.95rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="email" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;"> {{ __('adminlte::adminlte.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" require
                        style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
                    @error('email')
                        <span style="color:#e3342f; font-size:0.95rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="password" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;">{{ __('adminlte::adminlte.password') }}</label>
                    <input id="password" type="password" name="password" required
                        style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
                    @error('password')
                        <span style="color:#e3342f; font-size:0.95rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="password_confirmation" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;">{{ __('adminlte::adminlte.password')  }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
                </div>

                <button type="submit" style="width:100%; background:#6C63FF; color:#fff; font-size:1.1rem; font-weight:600; border:none; border-radius:24px; padding:14px 0; margin-bottom:18px; cursor:pointer; box-shadow:0 4px 16px 0 rgba(108,99,255,0.15); transition:background 0.2s;">
              {{__('adminlte::adminlte.register')}}
                </button>
            </form>

        </div>
    </div>
</div>
