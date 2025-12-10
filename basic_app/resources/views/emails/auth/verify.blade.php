@php
    use App\Helpers\CustomSettings;

    $s = CustomSettings::appSettings();

    $locale = $locale ?? app()->getLocale();
    $dir    = in_array(strtolower($locale), ['ar','he','fa','ur']) ? 'rtl' : 'ltr';
    $align  = $dir === 'rtl' ? 'right' : 'left';

    $brand = [
        'main'         => $s['main_color']        ?? '#ff7e00',   // primary
        'sub'          => $s['sub_color']         ?? '#fff7ef',   // soft bg
        'text'         => $s['text_color']        ?? '#1f2933',
        'button'       => $s['button_color']      ?? '#ff7e00',
        'button_text'  => $s['button_text_color'] ?? '#ffffff',
    ];

    $companyName = $s['name_' . ($locale === 'ar' ? 'ar' : 'en')] ?? 'Ecommerce App';
    $logoUrl     = $s['image'] ? asset('storage/' . $s['image']) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ 'Verify your email for ' . $companyName }}</title>

    <style>
        /* Reset-ish */
        body {
            margin: 0;
            padding: 0;
            background: #f6f4f0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: {{ $brand['text'] }};
            text-align: {{ $align }};
            direction: {{ $dir }};
        }
        img {
            border: 0;
            line-height: 100%;
            max-width: 100%;
        }

        /* Layout */
        .email-shell {
            width: 100%;
            padding: 24px 12px;
        }
        .email-container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
        }

        /* Card */
        .card {
            border-radius: 20px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
            border: 1px solid rgba(148, 163, 184, .15);
        }

        /* Header / hero */
        .card-header {
            padding: 28px 26px 18px;
            text-align: center;
            background: linear-gradient(
                135deg,
                {{ $brand['sub'] }},
                #ffffff
            );
        }
        .brand-logo {
            height: 52px;
            width: auto;
            margin-bottom: 10px;
        }
        .brand-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: {{ $brand['main'] }};
        }
        .brand-subtitle {
            margin: 4px 0 0;
            font-size: 13px;
            color: #6b7280;
        }

        /* Illustration strip */
        .hero-illustration {
            border-top: 1px solid rgba(148, 163, 184, .15);
            border-bottom: 1px solid rgba(148, 163, 184, .15);
            background: radial-gradient(circle at top, {{ $brand['sub'] }} 0, #ffffff 60%);
        }
        .hero-illustration img {
            display: block;
            margin: 0 auto;
            max-width: 260px;
            padding: 12px 0;
        }

        /* Content */
        .card-body {
            padding: 28px 26px 24px;
        }
        .pill-tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            background: rgba(255, 126, 0, .08);
            color: {{ $brand['main'] }};
            font-weight: 600;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 22px;
            margin: 0 0 10px;
            color: {{ $brand['text'] }};
        }
        .lead {
            font-size: 15px;
            line-height: 1.7;
            margin: 0 0 10px;
            color: #4b5563;
        }
        .muted {
            font-size: 13px;
            line-height: 1.6;
            color: #9ca3af;
            margin: 0 0 14px;
        }

        /* CTA block */
        .cta-box {
            margin: 20px 0 24px;
            padding: 16px 16px 18px;
            border-radius: 14px;
            background: #faf5ff;
            border: 1px solid rgba(129, 140, 248, .25);
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: {{ $brand['button'] }};
            color: {{ $brand['button_text'] }};
            text-decoration: none;
            font-weight: 600;
            border-radius: 999px;
            font-size: 15px;
            line-height: 1.2;
            margin: 4px 0 0;
        }
        .btn:hover {
            opacity: .9;
        }
        .cta-helper {
            font-size: 12px;
            margin-top: 8px;
            color: #6b7280;
        }

        /* Alt URL */
        .alt-url {
            font-size: 12px;
            color: #9ca3af;
            word-break: break-all;
            margin-top: 10px;
        }

        /* Footer */
        .footer {
            margin-top: 16px;
            font-size: 12px;
            text-align: center;
            color: #9ca3af;
            line-height: 1.6;
        }
        .footer a {
            color: {{ $brand['main'] }};
            text-decoration: none;
            font-weight: 500;
        }

        /* Small text tweaks for mobile */
        @media (max-width: 480px) {
            .card-body {
                padding: 22px 18px 20px;
            }
            h1 {
                font-size: 19px;
            }
            .btn {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Hidden preheader -->
<div style="display:none;opacity:0;height:0;overflow:hidden;">
    {{ 'Verify your email for ' . $companyName }}
</div>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" class="email-shell">
    <tr>
        <td align="center">
            <div class="email-container">

                <div class="card">
                    {{-- HEADER --}}
                    <div class="card-header">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $companyName }}" class="brand-logo">
                        @else
                            <h2 class="brand-title">{{ $companyName }}</h2>
                        @endif

                        <p class="brand-subtitle">
                            {{ 'Verify your email for ' . $companyName }}
                        </p>
                    </div>

                    {{-- ILLUSTRATION --}}
                    <div class="hero-illustration">
                        <img src="{{ asset('images/imageverify.jpg') }}" alt="">
                    </div>

                    {{-- CONTENT --}}
                    <div class="card-body">
                        <span class="pill-tag">
                            Verify Email
                        </span>

                        <h1>Verify your email address</h1>

                        <p class="lead">
                            {{ 'Hello ' . ($user->name ?? 'User') . ', please verify your email address.' }}
                        </p>

                        <p class="lead">
                            Click the button below to verify your email address.
                        </p>

                        <div class="cta-box">
                            <p class="cta-helper">
                                Click the button to verify your email.
                            </p>
                            <a href="{{ $verificationUrl }}" class="btn">
                                Verify Email
                            </a>
                        </div>

                        <p class="muted">
                            If you didn't create an account, no further action is required.
                        </p>

                        <p class="alt-url">
                            If the button doesn't work, copy and paste this URL into your browser: {{ $verificationUrl }}
                        </p>
                    </div>

                    {{-- FOOTER --}}
                    <div class="footer">
                        You're receiving this email because you created an account at {{ $companyName }}.<br>
                        <a href="{{ url('/') }}">{{ $companyName }}</a>
                    </div>
                </div>

            </div>
        </td>
    </tr>
</table>
</body>
</html>
