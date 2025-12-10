@php
    use App\Helpers\CustomSettings;

    // Fetch company settings from CompanyInfo
    $settings = CustomSettings::appSettings();

    // Brand colors from company info (with safe fallbacks)
    $brand = [
        // Primary accent / button color
        'main_color' => $settings['button_color']
            ?? $settings['main_color']
            ?? '#2563EB',

        // Header / top bar color
        'sub_color'  => $settings['sub_color']
            ?? '#111827',

        // Main text color
        'text_color' => $settings['text_color']
            ?? '#1F2933',

        // Card background
        'card_color' => $settings['card_color']
            ?? '#FFFFFF',

        // Page background (slightly soft)
        'bg_soft'    => $settings['text_filed_color']
            ?? '#F6F7FB',
    ];

    // App name & logo from CompanyInfo, with fallbacks
    $appName = $appName
        ?? ($settings['name_en'] ?? config('app.name', 'My App'));

    $logoUrl = $logoUrl
        ?? (!empty($settings['image']) ? asset($settings['image']) : null);

    $userName  = $user->name ?? 'User';
    $preheader = $preheader ?? "Reset your {$appName} password.";
@endphp

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Reset your password - {{ $appName }}</title>

    <style>
        /* Email-safe button style */
        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: #ffffff !important;
            background: {{ $brand['main_color'] }};
            font-weight: 700;
            font-size: 14px;
        }

        .muted {
            color: #6B7280;
            font-size: 12px;
        }

        .card {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        h1, h2, p {
            margin: 0;
        }

        /* Dark mode adjustments */
        @media (prefers-color-scheme: dark) {
            body { background: #0b0d12 !important; color: #e5e7eb !important; }
            .card { background: #111827 !important; border-color: #1f2937 !important; }
            .muted { color: #9CA3AF !important; }
        }
    </style>
</head>

<body style="margin:0;padding:0;
             background:{{ $brand['bg_soft'] }};
             color:{{ $brand['text_color'] }};
             direction:ltr;
             text-align:left;">

    <!-- Hidden preheader (shows as preview text in email inbox) -->
    <div style="display:none;opacity:0;max-height:0;overflow:hidden;">
        {{ $preheader }}
    </div>

    <!-- Outer wrapper -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
           style="background:{{ $brand['bg_soft'] }};padding:24px 0;">
        <tr>
            <td align="center">

                <!-- Email card -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0"
                       class="card"
                       style="background:{{ $brand['card_color'] }};">
                    <!-- Header (brand/logo area) -->
                    <tr>
                        <td style="padding:20px;background:{{ $brand['sub_color'] }};">
                            <table width="100%" role="presentation">
                                <tr>
                                    <td style="text-align:left;">
                                        @if(!empty($logoUrl))
                                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" width="120" style="display:block">
                                        @else
                                            <h1 style="margin:0;color:#ffffff;font-size:20px;">
                                                {{ $appName }}
                                            </h1>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:28px 24px;">
                            <h2 style="margin:0 0 12px 0;font-size:22px;">
                                Reset your password
                            </h2>

                            <p style="margin:0 0 10px 0;line-height:1.6;font-size:14px;">
                                Hi {{ $userName }},
                            </p>

                            <p style="margin:0 0 14px 0;line-height:1.6;font-size:14px;">
                                We received a request to reset the password for your {{ $appName }} account.
                            </p>

                            <p style="margin:0 0 14px 0;line-height:1.6;font-size:14px;">
                                To choose a new password, click the button below. For your security,
                                this link will only be valid for {{ $expiresIn ?? 60 }} minutes.
                            </p>

                            @if(!empty($resetUrl))
                                <p style="margin:0 0 24px 0;text-align:center;">
                                    <a href="{{ $resetUrl }}" class="btn" target="_blank" rel="noopener">
                                        Reset password
                                    </a>
                                </p>
                            @endif

                            <p class="muted" style="margin:0 0 8px 0;line-height:1.6;">
                                If the button above doesn’t work, copy and paste this link into your browser:
                            </p>

                            @if(!empty($resetUrl))
                                <button class="muted" style="margin:0 0 8px 0;line-height:1.6;word-break:break-all;">
                                    {{ $resetUrl }}
                                </button>
                            @endif

                            <p class="muted" style="margin:8px 0 0 0;line-height:1.6;">
                                If you did not request a password reset, you can safely ignore this email and
                                your password will remain unchanged.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:16px 24px;border-top:1px solid #e5e7eb;">
                            <p class="muted" style="margin:0;">
                                You received this email because you have an account with {{ $appName }}.
                                If this wasn’t you, you can ignore this message.
                            </p>
                            <p class="muted" style="margin:4px 0 0 0;">
                                &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Email card -->

            </td>
        </tr>
    </table>
</body>
</html>
