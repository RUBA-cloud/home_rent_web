<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="form-basic">
  @csrf
  @if(isset($method) && in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
    @method($method)
  @endif

  {{-- Render color fields in a flex group --}}
  <div class="color-group">
    @foreach($fields as $f)
      @php
        $name     = $f['name'];
        $type     = $f['type'] ?? 'text';
        $label    = $f['label'] ?? ucfirst($name);
        $dir      = $f['dir'] ?? null;
        $id       = $f['id'] ?? $name;
        $value    = old($name, data_get($model, $name));
        $multiple = $f['multiple'] ?? false;
        $options  = $f['options'] ?? [];
      @endphp

      @if($type === 'color')
        <div class="color-field">
          <label for="{{ $id }}">{{ $label }}</label>
          <input
            type="color"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            class="form-control @error($name) is-invalid @enderror"
          >
          @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      @endif
    @endforeach
  </div>

  {{-- Other fields --}}
  @foreach($fields as $f)
    @php
      $name     = $f['name'];
      $type     = $f['type'] ?? 'text';
      $label    = $f['label'] ?? ucfirst($name);
      $dir      = $f['dir'] ?? null;
      $id       = $f['id'] ?? $name;
      $value    = old($name, data_get($model, $name));
      $multiple = $f['multiple'] ?? false;
      $options  = $f['options'] ?? [];
    @endphp

    @continue($type === 'color') {{-- Skip already rendered color fields --}}

    <div class="form-group">
      @if($type === 'text' && $name === 'image')

      @elseif($type === 'text' && $name === 'working_days')
        <x-working-days-hours
          :branch_working_days="old('branch_working_days')"
          :branch_working_hours_from="old('branch_working_hours_from')"
          :branch_working_hours_to="old('branch_working_hours_to')"
        />

      @elseif($type === 'select')
        <label for="{{ $id }}">{{ $label }}</label>
        <select
          id="{{ $id }}"
          name="{{ $name }}{{ $multiple ? '[]' : '' }}"
          class="custom-select @error($name) is-invalid @enderror"
          {{ $multiple ? 'multiple' : '' }}
        >
          @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected($optValue == $value)>
              {{ $optLabel }}
            </option>
          @endforeach
        </select>
        @error($name)
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

      @else
        <label for="{{ $id }}">{{ $label }}</label>
        <input
          type="{{ $type }}"
          name="{{ $name }}{{ $multiple ? '[]' : '' }}"
          id="{{ $id }}"
          dir="{{ $dir }}"
          value="{{ $value }}"
          class="form-control @error($name) is-invalid @enderror"
          {{ $multiple ? 'multiple' : '' }}
        >
        @error($name)
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      @endif
    </div>
  @endforeach

  {{-- is_active checkbox --}}
  <div class="form-check">
    @php
      $checked = old('is_active', data_get($model, 'is_active', false)) ? true : false;
    @endphp
    <input
      class="form-check-input @error('is_active') is-invalid @enderror"
      type="checkbox"
      name="is_active"
      id="is_active"
      value="1"
      @checked($checked)
    > Active

    @error('is_active')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <button type="submit" class="btn_secondary">
    {{ isset($model) && data_get($model, 'id') ? 'Update' : 'Save' }}
  </button>
</form>
<style>
    /* Main Select Box Style */
.custom-select {
    -webkit-appearance: none;  /* Remove default styling */
    -moz-appearance: none;     /* Remove default styling for Firefox */
    appearance: none;          /* General reset */
    padding: 12px 30px 12px 15px; /* Adjust padding for nice spacing */
    border: 1px solid #ccc;   /* Light gray border */
    border-radius: 8px;       /* Rounded corners */
    background-color: #fff;   /* White background */
    font-size: 16px;           /* Increase font size for readability */
    color: #333;               /* Dark text color */
    width: 100%;               /* Ensure it fills the width of its container */
    cursor: pointer;          /* Change cursor to pointer to indicate it's clickable */
    position: relative;        /* Positioning for the custom arrow */
    transition: border-color 0.2s ease; /* Smooth transition for border */
}

/* Focus effect when select is clicked */
.custom-select:focus {
    outline: none;
    border-color: #007bff; /* Blue border on focus */
}

/* Custom Arrow */
.custom-select::after {
    content: ''; /* Empty content for the arrow */
    position: absolute;
    right: 10px; /* Position it at the right */
    top: 50%;
    transform: translateY(-50%); /* Vertically center the arrow */
    border-left: 5px solid transparent; /* Left side triangle */
    border-right: 5px solid transparent; /* Right side triangle */
    border-top: 5px solid #333; /* Dark arrow color */
    pointer-events: none; /* Don't let the arrow interfere with clicking */
}
 /* ========= CHAT PAGE STYLING (DARK CHAT LAYOUT) ========= */

.chat-page {
    font-size: .95rem;
    background: #020617;            /* خلفية عامة للشات */
    color: #e5e7eb;
}

.chat-shell {
    background: #020617;
    border-radius: 1rem;
    overflow: hidden;
    border: 1px solid #1f2937;
}

.chat-grid {
    display: grid;
    min-height: 75vh;
    grid-template-areas: "users conv";
    grid-template-columns: 280px 1fr;
    background: #020617;
}
@media (max-width: 992px) {
    .chat-grid {
        grid-template-columns: 1fr;
        grid-template-areas:
          "users"
          "conv";
    }
}

/* LTR MODE */
.chat-page.ltr-mode .chat-grid {
    direction: ltr;
}
.chat-page.ltr-mode .users-pane {
    grid-area: users;
    border-right: 1px solid #1f2937;
    border-left: none;
}
.chat-page.ltr-mode .conv-pane {
    grid-area: conv;
}

/* RTL MODE */
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
    border-left: 1px solid #1f2937;
    border-right: none;
}
.chat-page.rtl-mode .conv-pane {
    grid-area: conv;
}

/* USERS PANE (القائمة الجانبية لليوزرز) */
.users-pane {
    background: #020617;
    display: flex;
    flex-direction: column;
}

.users-head {
    padding: .75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    border-bottom: 1px solid #1f2937;
    background: #0b1120;
    color: #e5e7eb;
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
    color: #e5e7eb;
    transition: background .15s, border-color .15s, transform .1s;
}
.user-row:hover {
    background: #111827;
    transform: translateX(1px);
}
.user-row.active {
    background: #0f172a;
    border: 1px solid #1d4ed8;
}

.user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #111827;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; color: #60a5fa;
    overflow: hidden;
}
.user-avatar img { width: 100%; height: 100%; object-fit: cover; }

.user-name { font-weight: 600; }

.badge-unread {
    background: #2563eb;
    color: #fff;
    border-radius: 999px;
    font-size: .75rem;
    padding: .15rem .5rem;
}

/* RTL tweaks */
.chat-page.rtl-mode .users-head {
    flex-direction: row-reverse;
}
.chat-page.rtl-mode .users-head-title,
.chat-page.rtl-mode .users-head-actions {
    flex-direction: row-reverse;
}

/* CONVERSATION PANE */
.conv-pane {
    display: flex;
    flex-direction: column;
    background: #020617;
}

.conv-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .8rem 1rem;
    border-bottom: 1px solid #1f2937;
    background: #020617;
}

.conv-head-main {
    display: flex;
    align-items: center;
    gap: .6rem;
}

.peer-avatar {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    overflow: hidden;
    background: #111827;
}
.peer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.conv-title {
    font-weight: 600;
    font-size: 1.05rem;
    color: #e5e7eb;
}

.conv-body {
    flex: 1;
    background: radial-gradient(circle at top, #020617 0, #020617 50%, #000 100%);
    padding: 1rem;
    overflow-y: auto;
}

/* input bar */
.conv-input {
    background: #020617;
    padding: .75rem 1rem;
    border-top: 1px solid #1f2937;
}

.conv-input-form {
    display: flex;
    gap: .5rem;
    align-items: center;
}

.conv-message,
.conv-recipient {
    background: #020617;
    border: 1px solid #1f2937;
    color: #e5e7eb;
}
.conv-message:focus,
.conv-recipient:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 1px rgba(37,99,235,.5);
}

.conv-send-btn {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
}
.conv-send-btn:hover {
    filter: brightness(1.05);
}

/* day divider */
.day-divider {
    text-align: center;
    color: #6b7280;
    font-size: .8rem;
    margin: .8rem 0;
}

/* RTL conv head */
.chat-page.rtl-mode .conv-head-main {
    flex-direction: row-reverse;
}
.chat-page.rtl-mode .conv-title {
    text-align: right;
}

/* MESSAGES */
.msg {
    display: flex;
    gap: .6rem;
    margin: .4rem 0;
    align-items: flex-end;
}

/* LTR: أنا يمين، هم يسار */
.msg.me {
    flex-direction: row-reverse;
}
.msg.them {
    flex-direction: row;
}

.avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #020617;
    border: 1px solid #1f2937;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; color: #60a5fa;
    overflow: hidden;
}
.avatar img { width: 100%; height: 100%; object-fit: cover; }

.bubble {
    max-width: 70%;
    padding: .6rem .8rem;
    border-radius: 1rem;
    font-size: .95rem;
    background: #020617;
    box-shadow: 0 6px 18px rgba(0,0,0,0.45);
    border: 1px solid #1f2937;
}

/* رسائلي */
.msg.me .bubble {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #e5e7eb;
    border-bottom-right-radius: .3rem;
    border-color: transparent;
}

/* رسائل الطرف الآخر */
.msg.them .bubble {
    background: #020617;
    color: #e5e7eb;
    border-bottom-left-radius: .3rem;
}

/* الوقت / الحالة */
.meta {
    font-size: .75rem;
    opacity: .7;
    margin-top: .2rem;
    display: block;
    text-align: right;
}

/* RTL message behavior: في العربي أعكس الاتجاه */
.chat-page.rtl-mode .msg.me {
    flex-direction: row;           /* أنا يسار */
}
.chat-page.rtl-mode .msg.them {
    flex-direction: row-reverse;  /* هم يمين */
}
.chat-page.rtl-mode .bubble .text {
    text-align: right;
}
.chat-page.rtl-mode .ms-2 {
    margin-left: 0 !important;
    margin-right: .5rem !important;
}

/* Input area RTL/LTR */
.chat-page.ltr-mode .conv-input-form {
    direction: ltr;
}
.chat-page.rtl-mode .conv-input-form {
    direction: rtl;
    flex-direction: row-reverse;
}
.chat-page.rtl-mode .conv-message,
.chat-page.rtl-mode .conv-recipient {
    text-align: right;
}
.chat-page.rtl-mode .conv-send-btn i {
    transform: scaleX(-1); /* flip paper-plane icon */
}

/* Hover Effect */
.custom-select:hover {
    border-color: #007bff; /* Highlight border on hover */
}

/* Optional: Style the option elements */
.custom-select option {
    padding: 10px;
    background-color: #fff;  /* Option background */
    color: #333;  /* Text color */
    font-size: 16px;
}

/* On Hover, Option Background Color */
.custom-select option:hover {
    background-color: #f1f1f1; /* Light hover color */
}
</style>
