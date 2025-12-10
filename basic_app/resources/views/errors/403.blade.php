{{-- resources/views/errors/403.blade.php --}}
@extends('adminlte::page')

@php
    use App\Helpers\CustomSettings;

    $s = CustomSettings::appSettings();
    $isRtl = app()->isLocale('ar');

    $mainColor = $s['main_color'] ?? '#4A90E2';
    $subColor  = $s['sub_color'] ?? '#E6F0FA';
    $textColor = $s['text_color'] ?? '#1F2937';
@endphp

@section('title', $isRtl ? 'غير مصرح بالدخول' : 'Access Denied')

@section('content')
<div class="error-wrapper d-flex align-items-center justify-content-center" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="error-card text-center shadow-lg p-5">
        <div class="error-icon mb-4">
            <i class="fas fa-ban fa-5x" style="color:{{ $mainColor }}"></i>
        </div>

        <h1 class="error-code" style="color:{{ $mainColor }}">403</h1>
        <h3 class="error-title mb-3" style="color:{{ $textColor }}">
            {{ $isRtl ? 'ليس لديك صلاحية الوصول إلى هذه الصفحة' : 'You don’t have permission to access this page' }}
        </h3>

        <p class="error-message text-muted mb-4">
            {{ $isRtl
                ? 'يبدو أنك تحاول الوصول إلى وحدة غير مفعّلة أو ليس لديك الصلاحيات الكافية.'
                : 'It seems you tried to access a disabled module or a page you don’t have permission for.' }}
        </p>

        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> {{ $isRtl ? 'رجوع' : 'Go Back' }}
            </a>
            <a href="{{ route('home') }}" class="btn btn-primary" style="background:{{ $mainColor }};border-color:{{ $mainColor }}">
                <i class="fas fa-home me-1"></i> {{ $isRtl ? 'الصفحة الرئيسية' : 'Home' }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.error-wrapper {
    min-height: calc(100vh - 120px);
    background: radial-gradient(circle at center, #ffffff 0%, #f5f7fa 100%);
}
.error-card {
    max-width: 550px;
    border-radius: 16px;
    background: #fff;
}
.error-code {
    font-size: 5rem;
    font-weight: 700;
    letter-spacing: 2px;
}
.error-title {
    font-weight: 600;
    line-height: 1.4;
}
.error-message {
    font-size: 1rem;
    line-height: 1.7;
}
.btn {
    padding: 0.6rem 1.3rem;
    font-weight: 500;
}
[dir="rtl"] .me-1 { margin-left: .25rem; margin-right: 0; }
[dir="rtl"] .me-2 { margin-left: .5rem; margin-right: 0; }
</style>
@endpush
