{{-- resources/views/admin/dashboard-colored.blade.php --}}
@extends('adminlte::master')

@php
    use App\Helpers\CustomSettings;

    $isRtl = app()->isLocale('ar');
    $s     = CustomSettings::appSettings();

    $contrast = function ($hex) {
        $h = ltrim($hex, '#');
        if (strlen($h) === 3) {
            $h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        }
        [$r, $g, $b] = [
            hexdec(substr($h,0,2)),
            hexdec(substr($h,2,2)),
            hexdec(substr($h,4,2)),
        ];
        $yiq = (($r*299)+($g*587)+($b*114))/1000;
        return $yiq >= 150 ? '#111111' : '#ffffff';
    };

    $mainColor       = $s['main_color'];
    $subColor        = $s['sub_color'];
    $textColor       = $s['text_color'];
    $iconColor       = $s['icon_color'];
    $fieldBg         = $s['text_filed_color'];
    $cardBg          = $s['card_color'];
    $hintColor       = $s['hint_color'];
    $labelColor      = $s['label_color'];
    $buttonTextColor = $s['button_text_color'];
    $buttonColor     = $s['button_color'];

    $onMain = $contrast($mainColor);
    $onSub  = $contrast($subColor);
@endphp

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

{{-- ✅ Add rtl class + dir attribute on <body> --}}
@section('classes_body', $layoutHelper->makeBodyClasses() . ($isRtl ? ' rtl' : ''))
@section('body_data', trim($layoutHelper->makeBodyData() . ' ' . ($isRtl ? 'dir="rtl"' : 'dir="ltr"')))

@section('adminlte_css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap4-theme/1.3.0/select2-bootstrap4.min.css" rel="stylesheet" />

    <style>
        :root{
          --brand-main: {{ $mainColor }};
          --brand-sub: {{ $subColor }};
          --brand-text: {{ $textColor }};
          --brand-icon: {{ $iconColor }};
          --field-bg: {{ $fieldBg }};
          --card-bg: {{ $cardBg }};
          --hint-color: {{ $hintColor }};
          --label-color: {{ $labelColor }};
          --btn-color: {{ $buttonColor }};
          --btn-text: {{ $buttonTextColor }};
          --on-main: {{ $onMain }};
          --on-sub: {{ $onSub }};
          --sidebar-width: 250px;
          --sidebar-mini-width: 4.6rem;
        }

        /* ✅ Make body RTL-friendly when class .rtl or html[dir=rtl] */
        body.rtl,
        html[dir="rtl"] body{
          direction: rtl;
          text-align: right;
        }

        body,
        .content-wrapper {
          color: var(--brand-text);
          background: var(--card-bg);
        }

        a {
          color: var(--brand-main);
        }
        a:hover {
          color: var(--brand-sub);
        }

        /* ===== LTR defaults (sidebar LEFT) ===== */
        .main-sidebar{
          position: fixed;
          top:0; bottom:0;
          left: 0; right: auto;
          width: var(--sidebar-width);
          background: #1f2937;
          transition: width .25s ease;
          z-index: 1040;
        }
        .content-wrapper,
        .main-header,
        .main-footer{
          margin-left: var(--sidebar-width);
          margin-right: 0;
          transition: margin .25s ease;
        }

        /* ===== Collapsed (both dirs) ===== */
        body.sidebar-collapse .main-sidebar{
          width: var(--sidebar-mini-width);
        }
        body.sidebar-collapse .content-wrapper,
        body.sidebar-collapse .main-header,
        body.sidebar-collapse .main-footer{
          margin-left: var(--sidebar-mini-width);
        }

        /* ===== RTL overrides (sidebar RIGHT) ===== */
        html[dir="rtl"] .main-sidebar{
          right: 0 !important;
          left: auto !important;
        }
        html[dir="rtl"] .content-wrapper,
        html[dir="rtl"] .main-header,
        html[dir="rtl"] .main-footer{
          margin-right: var(--sidebar-width) !important;
          margin-left: 0 !important;
        }
        html[dir="rtl"] body.sidebar-collapse .content-wrapper,
        html[dir="rtl"] body.sidebar-collapse .main-header,
        html[dir="rtl"] body.sidebar-collapse .main-footer{
          margin-right: var(--sidebar-mini-width) !important;
          margin-left: 0 !important;
        }

        /* Sidebar brand / logo */
        .main-sidebar .brand-link {
          background-color: var(--brand-main);
          color: var(--on-main);
          border-bottom: none;
        }
        .main-sidebar .brand-link .brand-text {
          font-weight: 600;
        }

        /* Sidebar menu colors */
        .sidebar {
          background: #111827;
          color: var(--brand-text);
        }
        .sidebar .nav-sidebar>.nav-item>.nav-link {
          color: var(--brand-text);
        }
        .sidebar .nav-sidebar>.nav-item>.nav-link.active {
          background-color: var(--brand-main);
          color: var(--on-main);
        }
        .sidebar .nav-sidebar>.nav-item>.nav-link i {
          color: var(--brand-icon);
        }

        /* ======= NAVBAR ======= */
        .main-header.navbar {
          background-color: var(--brand-main);
          color: var(--on-main);
        }
        .main-header .nav-link,
        .main-header .navbar-nav .nav-link {
          color: var(--on-main);
        }
        .main-header .nav-link:hover {
          color: var(--on-sub);
        }

        /* ======= FORM / FIELDS ======= */
        .form-control {
          background-color: var(--field-bg);
          border-color: rgba(0,0,0,.08);
          color: var(--brand-text);
        }
        .form-control:focus {
          box-shadow: 0 0 0 0.1rem rgba(0,0,0,.05);
          border-color: var(--brand-main);
        }

        .form-text {
          color: var(--hint-color);
        }
        label {
          color: var(--label-color);
        }

        /* ======= BUTTONS ======= */
        .btn-primary,
        .btn-success,
        .btn-info {
          background-color: var(--btn-color);
          border-color: var(--btn-color);
          color: var(--btn-text);
        }
        .btn-primary:hover,
        .btn-success:hover,
        .btn-info:hover {
          filter: brightness(0.95);
          color: var(--btn-text);
        }

        .btn-outline-secondary {
          color: var(--brand-text);
          border-color: rgba(0,0,0,.15);
        }

        /* ============ CARD STYLING ============ */

        /* Base card style uses themed card background */
        .card {
          background-color: var(--card-bg);
          color: var(--brand-text);
          border-color: rgba(0,0,0,.05);
        }

        /* Card header uses the same card background color + label color for text */
        .card-header {
          background-color: var(--card-bg);
          color: var(--label-color);
          border-bottom-color: rgba(0,0,0,.06);
        }

        /* Optional: emphasize title */
        .card-header .card-title {
          font-weight: 600;
        }

        /* ============ RTL: CARD ITEMS START FROM RIGHT ============ */

        /* When card header/body/footer use flex, reverse in RTL */
        html[dir="rtl"] .card .card-header.d-flex,
        html[dir="rtl"] .card .card-body.d-flex,
        html[dir="rtl"] .card .card-footer.d-flex {
          flex-direction: row-reverse;
        }

        /* Text inside cards right-aligned in RTL */
        html[dir="rtl"] .card .card-header,
        html[dir="rtl"] .card .card-body,
        html[dir="rtl"] .card .card-footer {
          text-align: right;
        }

        /* If you have tools on one side, keep them opposite to title */
        html[dir="rtl"] .card-header .card-tools {
          margin-right: 0;
          margin-left: auto;
        }

        /* Simple spacing for items in cards when RTL (e.g. flex items) */
        html[dir="rtl"] .card .d-flex > * {
          margin-left: .5rem;
          margin-right: 0;
        }

        /* ============ RTL: SEARCH ICON ON THE RIGHT ============ */

        /* Navbar search input-group: reverse it in Arabic */
        html[dir="rtl"] .navbar .form-inline .input-group,
        html[dir="rtl"] .navbar-search-block .input-group {
          flex-direction: row-reverse;
        }

        /* Make the search text itself right-aligned in Arabic */
        html[dir="rtl"] .navbar .form-inline .form-control,
        html[dir="rtl"] .navbar-search-block .form-control {
          text-align: right;
        }

        /* Adjust prepend/append spacing when reversed */
        html[dir="rtl"] .navbar .input-group .input-group-append,
        html[dir="rtl"] .navbar-search-block .input-group-append {
          margin-right: .25rem;
          margin-left: 0;
        }

        /* Generic input-group (e.g. search cards) */
        html[dir="rtl"] .input-group {
          direction: rtl;
        }
        html[dir="rtl"] .input-group .form-control {
          text-align: right;
        }
        html[dir="rtl"] .input-group .input-group-prepend,
        html[dir="rtl"] .input-group .input-group-append {
          flex-direction: row-reverse;
        }

        /* Footers */
        .main-footer {
          background-color: var(--card-bg);
          border-top-color: rgba(0,0,0,.08);
          color: var(--hint-color);
        }
        .chat-page {
  font-size: .95rem;
}
.chat-shell {
  background: #ffffff;
  border-radius: 1rem;
  overflow: hidden;
}
.chat-grid {
  display: grid;
  min-height: 75vh;
  background: #f3f4f6;
  grid-template-areas: "users conv";
  grid-template-columns: 280px 1fr;
}
@media (max-width: 992px) {
  .chat-grid {
    grid-template-columns: 1fr;
    grid-template-areas:
      "users"
      "conv";
  }
}

/* ====== LTR MODE (DEFAULT) ====== */
.chat-page.ltr-mode .chat-grid {
  direction: ltr;
}
.chat-page.ltr-mode .users-pane {
  grid-area: users;
  border-right: 1px solid #e5e7eb;
  border-left: none;
}
.chat-page.ltr-mode .conv-pane {
  grid-area: conv;
}

/* ====== RTL MODE (ARABIC) ====== */
.chat-page.rtl-mode .chat-grid {
  direction: rtl;
  grid-template-areas: "conv users";
  grid-template-columns: 1fr 280px;
}
@media (max-width: 992px) {
  .chat-page.rtl-mode .chat-grid {
    grid-template-columns: 1fr;
    grid-template-areas:
      "users"
      "conv";
  }
}
.chat-page.rtl-mode .users-pane {
  grid-area: users;
  border-left: 1px solid #e5e7eb;
  border-right: none;
}
.chat-page.rtl-mode .conv-pane {
  grid-area: conv;
}

/* ====== USERS PANE ====== */
.users-pane {
  background: #f9fafb;
  display: flex;
  flex-direction: column;
}
.users-head {
  padding: .75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: .5rem;
  border-bottom: 1px solid #e5e7eb;
}
.users-list {
  flex: 1;
  overflow-y: auto;
  padding: .5rem;
}
.user-row {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .55rem .7rem;
  border-radius: .5rem;
  text-decoration: none;
  color: inherit;
  transition: background .15s, border-color .15s;
}
.user-row:hover { background: #eef2ff; }
.user-row.active {
  background: #dbeafe;
  border: 1px solid #bfdbfe;
}
.user-avatar {
  width: 42px; height: 42px;
  border-radius: 50%;
  background: #e0e7ff;
  display: flex; align-items: center; justify-content: center;
  font-weight: 600; color: #3730a3;
  overflow: hidden;
}
.user-avatar img { width: 100%; height: 100%; object-fit: cover; }
.user-name { font-weight: 600; }
.badge-unread {
  background: #2563eb; color: #fff;
  border-radius: 999px; font-size: .75rem;
  padding: .15rem .5rem; display: none;
}

/* RTL tweaks for users head */
.chat-page.rtl-mode .users-head {
  flex-direction: row-reverse;
}
.chat-page.rtl-mode .users-head-title {
  flex-direction: row-reverse;
}
.chat-page.rtl-mode .users-head-actions {
  flex-direction: row-reverse;
}

/* ====== CONVERSATION PANE ====== */
.conv-pane {
  display: flex;
  flex-direction: column;
  background: #ffffff;
}
.conv-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: .8rem 1rem;
  border-bottom: 1px solid #e5e7eb;
  background: #ffffff;
}
.conv-head-main {
  display: flex;
  align-items: center;
  gap: .6rem;
}
.peer-avatar {
  width: 36px;
  height: 36px;
}
.conv-title { font-weight: 600; font-size: 1.05rem; }

.conv-body {
  flex: 1;
  background: #f9fafb;
  padding: 1rem;
  overflow-y: auto;
}
.conv-input {
  background: #ffffff;
  padding: .75rem 1rem;
  border-top: 1px solid #e5e7eb;
}
.day-divider {
  text-align: center;
  color: #9ca3af;
  font-size: .8rem;
  margin: .8rem 0;
}

/* RTL – conversation head direction */
.chat-page.rtl-mode .conv-head-main {
  flex-direction: row-reverse;
}
.chat-page.rtl-mode .conv-title {
  text-align: right;
}

/* ====== MESSAGES – BASE (LTR) ====== */
.msg {
  display: flex;
  gap: .6rem;
  margin: .4rem 0;
  align-items: flex-end;
}
.msg.me {
  flex-direction: row-reverse;   /* me on RIGHT in LTR */
}
.msg.them {
  flex-direction: row;           /* them on LEFT in LTR */
}
.avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: #dbeafe;
  display: flex; align-items: center; justify-content: center;
  font-weight: 600; color: #1e3a8a;
  overflow: hidden;
}
.avatar img { width: 100%; height: 100%; object-fit: cover; }
.bubble {
  max-width: 70%;
  padding: .6rem .8rem;
  border-radius: 1rem;
  font-size: .95rem;
  background: #ffffff;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.msg.me .bubble {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: #ffffff;
  border-bottom-right-radius: .3rem;
}
.msg.them .bubble {
  border-bottom-left-radius: .3rem;
}
.meta {
  font-size: .75rem;
  opacity: .8;
  margin-top: .2rem;
  display: block;
  text-align: right;
}

/* ====== RTL MESSAGE BEHAVIOR ====== */
/* In RTL: me on LEFT, them on RIGHT */
.chat-page.rtl-mode .msg.me {
  flex-direction: row;           /* me LEFT now */
}
.chat-page.rtl-mode .msg.them {
  flex-direction: row-reverse;   /* them RIGHT now */
}

/* Align bubbles text to right in RTL */
.chat-page.rtl-mode .bubble .text {
  text-align: right;
}

/* Fix spacing util ms-2 in RTL */
.chat-page.rtl-mode .ms-2 {
  margin-left: 0 !important;
  margin-right: .5rem !important;
}

/* ====== INPUT AREA RTL/LTR ====== */
.chat-page.ltr-mode .conv-input-form {
  direction: ltr;
}
.chat-page.rtl-mode .conv-input-form {
  direction: rtl;
}
.chat-page.rtl-mode .conv-input-form {
  flex-direction: row-reverse;
}
.chat-page.rtl-mode .conv-message,
.chat-page.rtl-mode .conv-recipient {
  text-align: right;
}

/* Make send button hug text input nicely in RTL */
.chat-page.rtl-mode .conv-send-btn i {
  transform: scaleX(-1); /* flip the paper-plane icon */
}
    </style>
@endsection

@section('body')
    <div class="wrapper">

        @if($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        @unless($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endunless

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid py-2">
                    @yield('content')
                </div>
            </section>
        </div>

        @include('adminlte::partials.footer.footer')

        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.sidebar.control-sidebar')
        @endif
    </div>
@endsection

@section('adminlte_js')
    @parent

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="{{ asset('vendor/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

    <script>
        // ✅ expose PHP $isRtl to JS
        const isRtl = {!! $isRtl ? 'true' : 'false' !!};

        // Set <html dir="..."> to sync with locale
        document.documentElement.setAttribute('dir', isRtl ? 'rtl' : 'ltr');

        $(function () {
            // bs-custom-file-input
            if (window.bsCustomFileInput) {
                bsCustomFileInput.init();
            }

            // Standard Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Custom Select2 with RTL support
            $('.custom-select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                dir: isRtl ? 'rtl' : 'ltr',
                allowClear: true,
                placeholder: @json(__('adminlte::adminlte.select')),
            });
        });

        // If you have Pusher/Echo config, you can add it here
        // Pusher.logToConsole = false;
        // const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
        //     cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
        //     forceTLS: true
        // });
    </script>
@endsection
