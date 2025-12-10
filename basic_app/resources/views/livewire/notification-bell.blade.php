{{-- resources/views/livewire/notifications/bell.blade.php --}}
@php use Illuminate\Support\Str; @endphp

<li class="nav-item dropdown" wire:poll.15s style="position: relative;">
    {{-- أيقونة الجرس + عدّاد غير المقروء --}}
    <a class="nav-link disabled" tabindex="-1" aria-disabled="true" href="#">
        <i class="far fa-bell"></i>
        @if($this->count > 0)
            <span class="badge navbar-badge badge-danger bg-danger">{{ $this->count }}</span>
        @endif
    </a>

    {{-- القائمة دائماً ظاهرة (لا تعتمد على dropdown من Bootstrap) --}}
    <div
        id="notifDropdownMenu"
        style="
            max-width: 360px;
            max-height: 480px;
            overflow: hidden;
            display: block;           /* force visible */
            position: absolute;
            top: 100%;                /* تحت أيقونة الجرس مباشرة */
            {{ app()->getLocale()==='ar' ? 'left:0;' : 'right:0;' }}
            z-index: 1035;            /* فوق الـ navbar */
        "
        role="menu"
        aria-labelledby="notifDropdownToggle"
    >
        {{-- العنوان + زر "تعيين الكل كمقروء" --}}
        <div class="px-3 py-2 d-flex align-items-center justify-content-between">
            <span class="fw-bold">
                {{ __('adminlte::adminlte.notifications') }}
            </span>

            <button class="btn btn-link btn-sm p-0"
                    wire:click="markAllAsRead"
                    wire:loading.attr="disabled">
                {{ __('adminlte::adminlte.mark_all_as_read') }}
            </button>
        </div>

        {{-- قائمة الإشعارات --}}
        <div class="list-group list-group-flush" style="max-height: 380px; overflow:auto;">
            @forelse($items as $n)
                <a
                    style="margin: 5px"
                    href="{{ $n->link ?: '#' }}"
                    wire:key="notification-{{ $n->id }}"
                    @class([
                        'list-group-item',
                        'list-group-item-action',
                        'bg-light' => is_null($n->read_at),
                    ])
                    wire:click.prevent="markAsRead({{ $n->id }})"
                >
                    <div class="d-flex align-items-start" style="margin: 5px">
                        <i class="{{ $n->icon ?: 'fas fa-bell' }} mr-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $n->title }}</strong>
                                <small class="text-muted">
                                    {{ $n->created_at->diffForHumans() }}
                                </small>
                            </div>
                            @if($n->body)
                                <div class="small text-muted">
                                    {{ Str::limit($n->body, 120) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center text-muted py-3">
                    {{ __('adminlte::adminlte.no_notification') }}
                </div>
            @endforelse
        </div>

        <div class="dropdown-divider m-0"></div>

        {{-- أسفل القائمة: "عرض الكل" + "تحميل المزيد" --}}
        <div class="px-3 py-2 d-flex justify-content-between align-items-center">
            <a href="{{ route('notifications.index') }}" class="small">
                {{ __('adminlte::adminlte.view_all') }}
            </a>

            @if($items->hasMorePages())
                <button class="btn btn-sm btn-outline-secondary"
                        wire:click="$set('perPage', {{ $perPage + 8 }})"
                        wire:loading.attr="disabled">
                    {{ __('adminlte::adminlte.load_more') }}
                </button>
            @endif
        </div>
    </div>
</li>
