@php
    $dir = in_array(strtolower($locale ?? app()->getLocale()), ['ar','he','fa','ur']) ? 'rtl' : 'ltr';
    $align = $dir === 'rtl' ? 'right' : 'left';
    $reverse = $dir === 'rtl' ? 'rtl' : 'ltr';
    $brand = $colors ?? ['main_color'=>'#FF2D20','sub_color'=>'#1A202C','text_color'=>'#22223B'];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}" dir="{{ $dir }}">
<head>
  <meta charset="utf-8">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <title>{{ __('adminlte::adminlte.verify_email_subject', ['app' => $appName]) }}</title>
  <style>
    /* Minimal email-safe CSS */
    .btn{
      display:inline-block;padding:12px 20px;border-radius:8px;
      text-decoration:none;color:#fff;background:{{ $brand['main_color'] }};
      font-weight:700
    }
    .muted{color:#6B7280;font-size:12px}
    @media (prefers-color-scheme: dark){
      body{background:#0b0d12!important;color:#e5e7eb!important}
      .card{background:#111827!important;border-color:#1f2937!important}
      .muted{color:#9CA3AF!important}
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;color:{{ $brand['text_color'] }};direction:{{ $dir }};text-align:{{ $align }}">
  <!-- Preheader (hidden in most clients) -->
  <div style="display:none;opacity:0;max-height:0;overflow:hidden;">
    {{ $preheader ?? '' }}
  </div>

  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f7fb;padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" class="card" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden">
          <tr>
            <td style="padding:20px;background:{{ $brand['sub_color'] }};">
              <table width="100%">
                <tr>
                  <td style="text-align:{{ $align }};">
                    @if(!empty($logoUrl))
                      <img src="{{ $logoUrl }}" alt="{{ $appName }}" width="120" style="display:block">
                    @else
                      <h1 style="margin:0;color:#fff;font-size:20px">{{ $appName }}</h1>
                    @endif
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:28px 24px;">
              <h2 style="margin:0 0 12px 0;font-size:22px;">{{ __('adminlte::adminlte.verify_headline') }}</h2>
              <p style="margin:0 0 14px 0;line-height:1.6">{{ __('adminlte::adminlte.verify_intro', ['name' => $user->name ?? __('adminlte::adminlte.auth.user')]) }}</p>
              <p style="margin:0 0 24px 0;line-height:1.6">{{ __('adminlte::adminlte.verify_cta_text') }}</p>

              <p style="margin:0 0 24px 0;">
                <a href="{{ $verificationUrl }}" class="btn">{{ __('adminlte::adminlte.verify_button') }}</a>
              </p>

              <p style="margin:0 0 8px 0;line-height:1.6" class="muted">
                {{ __('adminlte::adminlte.verify_alt', ['url' => $verificationUrl]) }}
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:16px 24px;border-top:1px solid #e5e7eb;">
              <p class="muted" style="margin:0;">
                {{ __('adminlte::adminlte.email_footer_notice', ['app' => $appName]) }}
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
