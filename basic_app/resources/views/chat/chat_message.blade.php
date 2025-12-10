{{-- resources/views/admin/chat/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.chat'))

@php
    $isRtl  = app()->isLocale('ar');
    /** @var \App\Models\User $currentUser */
    $currentUser = $currentUser ?? Auth::user();

    // Broadcasting setup (same pattern as categories form)
    $broadcast = $broadcast ?? [
        'channel'        => 'chat.user.' . $currentUser->id,
        'events'         => ['message.sent'],
        'pusher_key'     => config('broadcasting.connections.pusher.key'),
        'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster', 'mt1'),
    ];
@endphp

{{-- ========== CUSTOM CHAT CSS ========== --}}
@section('adminlte_css')
    @parent
    <style>
        :root {
            --chat-bg: #f3f4f6;
            --chat-bg-dark: #0f172a;
            --chat-card-bg: #ffffff;
            --chat-card-bg-dark: #020617;
            --chat-border: #e5e7eb;
            --chat-border-dark: #1f2937;
            --chat-accent: #2563eb;
            --chat-accent-soft: rgba(37, 99, 235, 0.06);
            --chat-danger: #ef4444;
            --chat-success: #22c55e;
            --chat-text-muted: #6b7280;
            --chat-radius-lg: 18px;
            --chat-radius-xl: 22px;
            --chat-shadow-soft: 0 10px 30px rgba(15,23,42,.08);
        }

        body.dark-mode .chat-page {
            --chat-bg: var(--chat-bg-dark);
            --chat-card-bg: var(--chat-card-bg-dark);
            --chat-border: var(--chat-border-dark);
            --chat-text-muted: #9ca3af;
        }

        .chat-page {
            min-height: calc(100vh - 120px);
            background: radial-gradient(circle at top left, rgba(37,99,235,.08), transparent 55%),
                        radial-gradient(circle at bottom right, rgba(16,185,129,.06), transparent 55%),
                        var(--chat-bg);
            padding-block: 1.5rem;
        }

        .chat-page.rtl-mode {
            direction: rtl;
        }

        .chat-page.ltr-mode {
            direction: ltr;
        }

        .chat-shell {
            max-width: 1180px;
            margin-inline: auto;
            background: var(--chat-card-bg);
            border-radius: 22px;
            border: 1px solid var(--chat-border);
            box-shadow: var(--chat-shadow-soft);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .chat-grid {
            display: flex;
            min-height: 520px;
        }

        /* RTL: عكسي ترتيب الأعمدة */
        .chat-page.rtl-mode .chat-grid {
            flex-direction: row-reverse;
        }

        .users-pane {
            flex: 0 0 270px;
            max-width: 320px;
            border-inline-end: 1px solid var(--chat-border);
            background: linear-gradient(to bottom, rgba(15,23,42,0.01), rgba(15,23,42,0.03));
            display: flex;
            flex-direction: column;
        }

        .conv-pane {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at top, rgba(37,99,235,.04), transparent 60%),
                        var(--chat-card-bg);
        }

        .users-head {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid var(--chat-border);
            display: flex;
            flex-direction: column;
            gap: .55rem;
            background: rgba(15,23,42, 0.01);
        }

        .users-head-title span {
            font-size: 0.9rem;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .users-head-actions {
            width: 100%;
        }

        .users-head-actions .search {
            border-radius: 999px;
            font-size: 0.8rem;
        }

        .users-head-actions .btn {
            border-radius: 999px;
        }

        .users-list {
            flex: 1;
            overflow-y: auto;
            padding: .4rem 0 .6rem;
        }

        .users-list::-webkit-scrollbar {
            width: 5px;
        }
        .users-list::-webkit-scrollbar-thumb {
            background: rgba(148,163,184,.5);
            border-radius: 999px;
        }

        .user-row {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .55rem .95rem;
            text-decoration: none;
            color: inherit;
            position: relative;
            transition: background .18s ease, transform .12s ease;
        }

        .user-row:hover {
            background: var(--chat-accent-soft);
            transform: translateY(-1px);
        }

        .user-row.active {
            background: rgba(37,99,235,0.08);
            box-shadow: inset 2px 0 0 var(--chat-accent);
        }
        .chat-page.rtl-mode .user-row.active {
            box-shadow: inset -2px 0 0 var(--chat-accent);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.9);
        }

        .user-avatar img, .peer-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-lines {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 0.88rem;
            font-weight: 600;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .user-last {
            font-size: 0.7rem;
        }

        .user-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: .15rem;
        }

        .badge-unread {
            min-width: 20px;
            height: 20px;
            border-radius: 999px;
            padding-inline: .35rem;
            background: var(--chat-danger);
            color: #fff;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 2px rgba(255,255,255,.9);
        }

        .badge-unread:empty,
        .badge-unread[data-unread-for][data-unread-for="0"],
        .badge-unread[data-unread-for][data-unread-for=""] {
            display: none;
        }

        /* Conversation header */
        .conv-head {
            padding: .9rem 1.25rem;
            border-bottom: 1px solid var(--chat-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(6px);
            background: linear-gradient(to right, rgba(15,23,42,0.02), rgba(37,99,235,0.03));
        }

        .conv-head-main {
            display: flex;
            align-items: center;
            gap: .8rem;
        }

        .peer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: linear-gradient(135deg, #14b8a6, #0ea5e9);
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.95);
        }

        .conv-title {
            font-weight: 600;
            font-size: 0.98rem;
        }

        .conv-subtitle {
            font-size: 0.75rem;
            color: var(--chat-text-muted);
        }

        /* Conversation body */
        .conv-body {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 1.15rem 0.75rem;
            position: relative;
        }

        .conv-body::-webkit-scrollbar {
            width: 6px;
        }
        .conv-body::-webkit-scrollbar-thumb {
            background: rgba(148,163,184,.6);
            border-radius: 999px;
        }

        .day-divider {
            text-align: center;
            margin: .75rem auto 1rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--chat-text-muted);
            position: relative;
        }

        .day-divider::before,
        .day-divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 26%;
            height: 1px;
            background: rgba(148,163,184,.5);
        }

        .chat-page.ltr-mode .day-divider::before {
            left: 0;
        }
        .chat-page.ltr-mode .day-divider::after {
            right: 0;
        }
        .chat-page.rtl-mode .day-divider::before {
            right: 0;
        }
        .chat-page.rtl-mode .day-divider::after {
            left: 0;
        }

        .msg {
            display: flex;
            gap: .5rem;
            margin-bottom: .55rem;
        }

        .msg.me {
            justify-content: flex-end;
        }

        .msg.me .bubble {
            border-bottom-right-radius: 4px;
            border-bottom-left-radius: var(--chat-radius-xl);
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #fff;
        }

        .msg.them .bubble {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: var(--chat-radius-xl);
            background: #f3f4f6;
            color: #111827;
        }

        body.dark-mode .msg.them .bubble {
            background: #020617;
            border: 1px solid #1f2937;
            color: #e5e7eb;
        }

        .msg.me .avatar {
            order: 2;
        }

        .msg .avatar {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: radial-gradient(circle at 25% 25%, #38bdf8, #1d4ed8);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            margin-top: .12rem;
        }

        .bubble {
            max-width: 70%;
            padding: .45rem .65rem .35rem;
            border-radius: var(--chat-radius-lg);
            font-size: 0.83rem;
            position: relative;
            box-shadow: 0 4px 14px rgba(15,23,42,.08);
        }

        .bubble .text {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .bubble .meta {
            margin-top: .15rem;
            font-size: 0.7rem;
            opacity: .85;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .45rem;
        }

        .msg.them .meta {
            justify-content: flex-start;
        }

        .bubble.me .meta .from {
            opacity: .9;
        }

        /* Input area */
        .conv-input {
            padding: .7rem 1rem .85rem;
            background: rgba(15,23,42,0.02);
        }

        .conv-input-form {
            background: #f9fafb;
            border-radius: 999px;
            padding: .3rem .3rem .3rem .7rem;
            border: 1px solid var(--chat-border);
        }

        body.dark-mode .conv-input-form {
            background: #020617;
        }

        .conv-input select.form-select {
            border-radius: 999px;
            font-size: 0.8rem;
            padding-inline: .6rem;
            min-width: 180px;
        }

        .conv-message {
            border: none;
            background: transparent;
            box-shadow: none;
            font-size: 0.85rem;
        }

        .conv-message:focus {
            outline: none;
            box-shadow: none;
        }

        .conv-send-btn {
            border-radius: 999px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding-inline: .9rem !important;
            padding-block: .45rem !important;
        }

        .conv-send-btn i {
            font-size: .8rem;
            transform: translateX(1px);
        }

        /* Empty state */
        .conv-body .text-center.text-muted {
            margin-top: 2.5rem !important;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .chat-shell {
                border-radius: 16px;
            }
            .chat-grid {
                flex-direction: column;
            }
            .chat-page.rtl-mode .chat-grid {
                flex-direction: column;
            }
            .users-pane {
                flex: 0 0 auto;
                max-width: none;
                border-inline-end: none;
                border-bottom: 1px solid var(--chat-border);
            }
            .conv-body {
                min-height: 320px;
            }
        }

        @media (max-width: 575.98px) {
            .users-head {
                padding-inline: .75rem;
            }
            .user-row {
                padding-inline: .75rem;
            }
            .conv-head,
            .conv-input {
                padding-inline: .75rem;
            }
            .bubble {
                max-width: 85%;
            }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid chat-page py-3 {{ $isRtl ? 'rtl-mode' : 'ltr-mode' }}">
  <div class="chat-shell">
    <div class="chat-grid">

      {{-- LEFT / RIGHT: USERS LIST (position depends on RTL/LTR) --}}
      <aside class="users-pane">
        <div class="users-head">
          <div class="users-head-title d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle p-2">
              <i class="fas fa-users text-primary"></i>
            </span>
            <div>
              <span class="fw-bold d-block">{{ __('adminlte::adminlte.users') }}</span>
              <small class="text-muted d-block">
                {{ __('adminlte::adminlte.choose_recipient') }}
              </small>
            </div>
          </div>

          <div class="users-head-actions d-flex gap-2 flex-grow-1">
            <input type="text" id="userSearch" class="form-control form-control-sm search"
                   placeholder="{{ __('adminlte::adminlte.search_users') }}">
            <a href="{{ route('chat.index') }}" class="btn btn-sm btn-light border" title="{{ __('adminlte::adminlte.refresh') }}">
              <i class="fas fa-sync-alt"></i>
            </a>
          </div>
        </div>

        <div class="users-list" id="usersList">
          @php
              $activeId = request('user_id');
          @endphp
          @foreach ($users as $u)
            @php
              $initials = collect(explode(' ', trim($u->name)))
                  ->take(2)
                  ->map(fn($p)=>mb_substr($p,0,1))
                  ->implode('');
              $isActive = (string)$activeId === (string)$u->id;
            @endphp
            <a href="{{ route('chat.index', ['user_id' => $u->id] + request()->except('page')) }}"
               class="user-row {{ $isActive ? 'active':'' }}"
               data-user-id="{{ $u->id }}">
              <div class="user-avatar">
                @if($u->avatar_path)
                  <img src="{{ asset($u->avatar_path) }}" alt="avatar">
                @else
                  {{ mb_strtoupper($initials ?: 'U') }}
                @endif
              </div>
              <div class="user-lines">
                <div class="user-name">{{ $u->name }}</div>
                <div class="user-last text-muted small">&nbsp;</div>
              </div>
              <div class="user-meta">
                <span class="badge-unread" data-unread-for="{{ $u->id }}">0</span>
              </div>
            </a>
          @endforeach
        </div>
      </aside>

      {{-- CONVERSATION --}}
      <section class="conv-pane">
        <div class="conv-head">
          @if($activeId)
            @php $peer = $users->firstWhere('id', (int)$activeId); @endphp
            <div class="conv-head-main">
              <div class="user-avatar peer-avatar">
                @if($peer?->avatar_path)
                  <img src="{{ asset($peer->avatar_path) }}" alt="avatar">
                @else
                  {{ mb_strtoupper(
                      collect(explode(' ', trim($peer?->name ?? 'U')))
                          ->take(2)
                          ->map(fn($p)=>mb_substr($p,0,1))
                          ->implode('')
                  ) }}
                @endif
              </div>
              <div>
                <div class="conv-title">{{ $peer?->name ?? __('adminlte::adminlte.conversation') }}</div>
                <div class="conv-subtitle">
                  <i class="fas fa-circle text-success me-1" style="font-size: .5rem"></i>
                  {{ __('adminlte::adminlte.active_chat') ?? '' }}
                </div>
              </div>
            </div>
          @else
            <div class="conv-head-main">
              <div>
                <div class="conv-title">{{ __('adminlte::adminlte.conversation') }}</div>
                <div class="conv-subtitle text-muted">
                  {{ __('adminlte::adminlte.choose_recipient') }}
                </div>
              </div>
            </div>
          @endif
        </div>

        <div id="chatBody"
             class="conv-body"
             data-current-user-id="{{ $currentUser->id }}"
             data-peer-user-id="{{ $activeId ?: '' }}"
             data-channel="{{ $broadcast['channel'] }}"
             data-events='@json($broadcast['events'])'>
          @php $lastDay = null; @endphp
          @forelse($messages as $m)
            @php
              $isMe   = $m->sender_id == $currentUser->id;
              $who    = $isMe ? 'me' : 'them';
              $day    = optional($m->created_at)->toDateString();
              $sender = $m->sender ?? null;
              $avatar = $isMe ? ($currentUser->avatar_path ?? null) : ($sender->avatar_path ?? null);
            @endphp

            @if($day !== $lastDay)
              <div class="day-divider">{{ \Carbon\Carbon::parse($day)->isoFormat('LL') }}</div>
              @php $lastDay = $day; @endphp
            @endif

            <div class="msg {{ $who }}" data-id="{{ $m->id }}">
              <div class="avatar"
                   title="{{ $sender?->name ?? ($isMe ? $currentUser->name ?? '' : '') }}">
                @if($avatar)
                  <img src="{{ asset($avatar) }}" alt="avatar">
                @else
                  {{ mb_strtoupper(
                      collect(explode(' ', trim($sender?->name ?? ($isMe ? ($currentUser->name ?? '') : 'U'))))
                          ->take(2)
                          ->map(fn($p)=>mb_substr($p,0,1))
                          ->implode('')
                  ) }}
                @endif
              </div>
              <div class="bubble {{ $who }}">
                <div class="text">{{ e($m->message) }}</div>
                <div class="meta">
                  <span class="time">{{ optional($m->created_at)->format('H:i') }}</span>
                  <span class="from ms-2 text-muted small">
                    {{ $isMe ? __('adminlte::adminlte.you') : ($sender?->name ?? __('adminlte::adminlte.user')) }}
                  </span>
                </div>
              </div>
            </div>
          @empty
            <div class="text-center text-muted my-3">
              <i class="far fa-comments mb-2 d-block" style="font-size: 1.8rem;"></i>
              {{ __('adminlte::adminlte.no_messages') }}
            </div>
          @endforelse
        </div>

        {{-- BROADCAST LISTENER ANCHOR (like category-form-listener) --}}
        <div id="chat-listener"
             data-channel="{{ $broadcast['channel'] }}"
             data-events='@json($broadcast['events'])'>
        </div>

        <div class="conv-input border-top">
          <form id="sendForm"
                action="{{ route('chat.store') }}"
                method="POST"
                class="conv-input-form d-flex align-items-center gap-2"
                autocomplete="off">
            @csrf
            @if($activeId)
              <input type="hidden" name="receiver_id" value="{{ (int)$activeId }}">
            @else
              <select name="receiver_id" class="form-select form-select-sm" required >
                <option value="">{{ __('adminlte::adminlte.choose_recipient') }}</option>
                @foreach($users as $u)
                  @continue($currentUser->id == $u->id)
                  <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>
                @endforeach
              </select>
            @endif
            <input type="text" name="message" class="form-control flex-fill conv-message"
                   placeholder="{{ __('adminlte::adminlte.type_message') }}" required maxlength="2000">
            <button type="submit" class="btn btn-primary px-3 conv-send-btn">
              <i class="fas fa-paper-plane"></i>
            </button>
          </form>
        </div>
      </section>

    </div>
  </div>
</div>
@endsection

@section('adminlte_js')
@parent
<script>
document.addEventListener('DOMContentLoaded', function () {
  const $ = (sel) => document.querySelector(sel);
  const chatBody   = $('#chatBody');
  const sendForm   = $('#sendForm');
  const usersList  = $('#usersList');
  const userSearch = $('#userSearch');
  const listener   = document.getElementById('chat-listener') || chatBody;

  const I18N = {
    now:  @json(__('adminlte::adminlte.now') ?? 'Now'),
    you:  @json(__('adminlte::adminlte.you') ?? 'You'),
    user: @json(__('adminlte::adminlte.user') ?? 'User'),
  };

  function formatTime(date) {
    const h = String(date.getHours()).padStart(2, '0');
    const m = String(date.getMinutes()).padStart(2, '0');
    return h + ':' + m;
  }

  /* ---------- SMART SCROLL ---------- */
  function nearBottom(el, threshold = 80) {
    if (!el) return true;
    return (el.scrollHeight - el.scrollTop - el.clientHeight) < threshold;
  }
  function smartScrollToBottom(el, force = false) {
    if (!el) return;
    if (force || nearBottom(el)) el.scrollTop = el.scrollHeight;
  }
  smartScrollToBottom(chatBody, true);
  window.addEventListener('load', ()=>smartScrollToBottom(chatBody, true));
  let rzTimer = null;
  window.addEventListener('resize', ()=>{
    clearTimeout(rzTimer);
    rzTimer = setTimeout(()=>smartScrollToBottom(chatBody), 120);
  });

  /* ---------- Users search ---------- */
  if (userSearch && usersList) {
    userSearch.addEventListener('input', function(){
      const q = this.value.toLowerCase().trim();
      usersList.querySelectorAll('.user-row').forEach(row=>{
        const name = (row.querySelector('.user-name')?.textContent || '').toLowerCase();
        row.style.display = name.includes(q) ? '' : 'none';
      });
    });
  }

  /* ---------- Optimistic send ---------- */
  function appendMine(text){
    const meName = @json($currentUser->name ?? Auth::user()->name);
    const avatar = @json($currentUser->avatar_path ?? null);
    const timeStr = formatTime(new Date());

    const wrap = document.createElement('div');
    wrap.className = 'msg me';
    wrap.innerHTML = `
      <div class="avatar" title="${meName || ''}">
        ${avatar
          ? `<img src="{{ asset('') }}${avatar}" alt="avatar">`
          : (meName ? meName.trim().split(' ').slice(0,2).map(s=>s[0]).join('').toUpperCase() : 'U')}
      </div>
      <div class="bubble me">
        <div class="text"></div>
        <div class="meta">
          <span class="time">${timeStr}</span>
          <span class="from ms-2 text-muted small">${I18N.you}</span>
        </div>
      </div>`;
    wrap.querySelector('.text').textContent = text;
    const stick = nearBottom(chatBody);
    chatBody.appendChild(wrap);
    smartScrollToBottom(chatBody, stick);
  }

  if (sendForm) {
    sendForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const fd  = new FormData(sendForm);
      const msg = (fd.get('message')||'').toString().trim();
      if (!msg) return;

      appendMine(msg);

      try {
        await fetch(sendForm.action, {
          method: 'POST',
          body: fd,
          credentials: 'same-origin',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content || ''
          }
        });
      } catch(err){
        console.error('[chat] send failed', err);
      }

      sendForm.message.value = '';
      sendForm.message.focus();
    });
  }

  /* ---------- Incoming messages (message.sent) ---------- */
  function appendIncoming(payload){
    let m = payload;

    if (m && typeof m === 'object') {
      if (m.message) m = m.message;
      else if (m.data && m.data.message) m = m.data.message;
    }
    if (!m || !chatBody) return;

    // Avoid duplicates by id
    if (m.id && chatBody.querySelector(`.msg[data-id="${m.id}"]`)) return;

    const currentUserId = Number(chatBody.dataset.currentUserId || 0);
    const peerUserId    = chatBody.dataset.peerUserId
      ? Number(chatBody.dataset.peerUserId)
      : null;

    const sender   = m.sender || {};
    const senderId = Number(m.sender_id ?? sender.id ?? 0);
    const recvId   = Number(m.receiver_id ?? m.receiver?.id ?? 0);
    const isMine   = senderId === currentUserId;
    if (isMine) return;

    const counterpart = (senderId === currentUserId) ? recvId : senderId;

    // If message is from/to another user, bump unread badge
    if (peerUserId && counterpart !== peerUserId) {
      const badge = document.querySelector(`.badge-unread[data-unread-for="${senderId}"]`);
      if (badge) {
        const curr = Number(badge.textContent) || 0;
        badge.textContent = String(curr + 1);
        badge.style.display = '';
      }
      return;
    }

    const name   = sender.name || I18N.user;
    const avatar = sender.avatar_path || null;
    const time   = m.created_at ? new Date(m.created_at) : new Date();
    const hhmm   = formatTime(time);

    const wrap = document.createElement('div');
    wrap.className = 'msg them';
    if (m.id) wrap.dataset.id = String(m.id);
    wrap.innerHTML = `
      <div class="avatar" title="${name}">
        ${avatar
          ? `<img src="{{ asset('') }}${avatar}" alt="avatar">`
          : (name ? name.trim().split(' ').slice(0,2).map(s=>s[0]).join('').toUpperCase() : 'U')}
      </div>
      <div class="bubble them">
        <div class="text"></div>
        <div class="meta">
          <span class="time">${hhmm}</span>
          <span class="from ms-2 text-muted small">${name}</span>
        </div>
      </div>`;
    wrap.querySelector('.text').textContent = (m.message ?? '');

    const stick = nearBottom(chatBody);
    chatBody.appendChild(wrap);
    smartScrollToBottom(chatBody, stick);
  }

  /* ---------- BROADCAST via AppBroadcast (same pattern as categories) ---------- */
  if (!listener) {
    console.warn('[chat] listener anchor not found');
    return;
  }

  const channelName = listener.dataset.channel || 'chat.user.' + (chatBody?.dataset.currentUserId || '');
  let events;

  try {
      events = JSON.parse(listener.dataset.events || '["message.sent"]');
  } catch (_) {
      events = ['message.sent'];
  }
  if (!Array.isArray(events) || !events.length) {
      events = ['message.sent'];
  }

  window.AppBroadcast = window.AppBroadcast || [];

  events.forEach(function (ev) {
      const handler = appendIncoming;

      if (typeof window.AppBroadcast.subscribe === 'function') {
          window.AppBroadcast.subscribe(channelName, ev, handler);
          console.info('[chat] listening via AppBroadcast →', channelName, '/', ev);
      } else {
          window.AppBroadcast.push({
              channel: channelName,
              event:   ev,
              handler: handler,
          });
          console.info('[chat] registered broadcast entry →', channelName, '/', ev);
      }
  });
});
</script>
@endsection
