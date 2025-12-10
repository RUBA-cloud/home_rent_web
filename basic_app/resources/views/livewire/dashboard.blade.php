{{-- resources/views/livewire/dashboard/custom-dashboard.blade.php --}}
@php
    $isRtl = app()->getLocale() === 'ar';

    $newOrdersCount       = $newOrders->count();
    $completedOrdersCount = $completedOrders->count();
    $newUsersCount        = $newUsers->count();
    $newProductsCount     = $newProducts->count();
@endphp

<div
    class="lw-dashboard container-fluid {{ $isRtl ? 'text-right' : '' }}"
    {{ $autoRefresh ? 'wire:poll.30s' : '' }}
>
    {{-- Top header / controls --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div class="mb-2">
            <div class="d-flex align-items-center mb-1">
                <div class="lw-dashboard-pill-icon mr-2 {{ $isRtl ? 'order-2 ml-0 ml-md-2' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h4 class="mb-0 font-weight-bold">
                    {{ __('adminlte::adminlte.dashboard') }}
                </h4>
            </div>
            <small class="text-muted">
                {{ __('adminlte::adminlte.live_snapshot') ?? __('adminlte::adminlte.latest_updates') }}
            </small>
        </div>

        <div class="d-flex flex-wrap align-items-center lw-dashboard-actions">
            {{-- Refresh button --}}
            <button
                wire:click="$dispatch('dashboard:refresh')"
                class="btn btn-outline-secondary btn-sm d-flex align-items-center mr-2 mb-2 lw-btn-soft"
            >
                <i class="fas fa-sync-alt {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                <span>{{ __('adminlte::adminlte.refresh') }}</span>
            </button>

            {{-- Auto refresh toggle --}}
            <button
                wire:click="$toggle('autoRefresh')"
                class="btn btn-sm mb-2 d-flex align-items-center lw-btn-soft
                    {{ $autoRefresh ? 'btn-success lw-btn-pulse' : 'btn-outline-secondary' }}"
            >
                <i class="far fa-clock {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                <span>
                    {{ $autoRefresh ? __('adminlte::adminlte.auto_refresh') : __('adminlte::adminlte.refresh_off') }}
                </span>
            </button>
        </div>
    </div>

    {{-- Quick stats strip --}}
    <div class="row mb-3 lw-stat-row">
        <div class="col-6 col-md-3 mb-2">
            <div class="lw-stat-card lw-stat-primary">
                <div class="lw-stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="lw-stat-body">
                    <span class="lw-stat-label">{{ __('adminlte::adminlte.newest_10_Orders') }}</span>
                    <span class="lw-stat-value">{{ $newOrdersCount }}</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 mb-2">
            <div class="lw-stat-card lw-stat-success">
                <div class="lw-stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="lw-stat-body">
                    <span class="lw-stat-label">{{ __('adminlte::adminlte.newest_10_completed_orders') }}</span>
                    <span class="lw-stat-value">{{ $completedOrdersCount }}</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 mb-2">
            <div class="lw-stat-card lw-stat-info">
                <div class="lw-stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="lw-stat-body">
                    <span class="lw-stat-label">{{ __('adminlte::adminlte.newest_10_users') }}</span>
                    <span class="lw-stat-value">{{ $newUsersCount }}</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 mb-2">
            <div class="lw-stat-card lw-stat-warning">
                <div class="lw-stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="lw-stat-body">
                    <span class="lw-stat-label">
                        {{ __('adminlte::adminlte.newest_10_products') ?? __('adminlte::adminlte.products') }}
                    </span>
                    <span class="lw-stat-value">{{ $newProductsCount }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders --}}
    <div class="row">
        {{-- Newest 10 Orders --}}
        <div class="col-12 col-xl-8 mb-3">
            <x-adminlte-card
                title="{{ __('adminlte::adminlte.newest_10_Orders') }}"
                theme="primary"
                icon="fas fa-shopping-cart"
                removable
                collapsible
                class="lw-list-card lw-data-card"
            >
                <div class="table-responsive-md lw-table-wrapper">
                    <table class="table table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte::adminlte.user_name') }}</th>
                                <th>{{ __('adminlte::adminlte.status') }}</th>
                                <th class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ __('adminlte::adminlte.Total') }}
                                </th>
                                <th>{{ __('adminlte::adminlte.created_at') }}</th>
                                <th style="width:1%;white-space:nowrap;" class="text-center">
                                    {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($newOrders as $o)
                            @php
                                $total = $o->total_price ?? $o->total ?? null;
                                $badge = match ($o->status) {
                                    'completed','done','paid' => 'success',
                                    'cancelled','rejected'    => 'danger',
                                    'pending'                 => 'warning',
                                    default                   => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td class="text-muted">
                                    #{{ $o->id }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $o->user->name ?? '-' }}</strong>
                                        @if(!empty($o->user?->email))
                                            <small class="text-muted">{{ $o->user->email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge lw-pill badge-{{ $badge }}">
                                        <i class="fas fa-circle small mr-1"></i>
                                        {{ $o->status_label ?? ucfirst($o->status) }}
                                    </span>
                                </td>
                                <td class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    <strong>{{ $money($total) }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($o->created_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="lw-actions">
                                        @if(Route::has('orders.show'))
                                            <a href="{{ route('orders.show', $o->id) }}"
                                               class="btn btn-sm btn-outline-primary lw-action-btn"
                                               title="{{ __('adminlte::adminlte.details') }}"
                                            >
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline">
                                                    {{ __('adminlte::adminlte.details') }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    {{ __('adminlte::adminlte.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Newest 10 Completed Orders --}}
        <div class="col-12 col-xl-4 mb-3">
            <x-adminlte-card
                title="{{ __('adminlte::adminlte.newest_10_completed_orders') }}"
                theme="success"
                icon="fas fa-check-circle"
                removable
                collapsible
                class="lw-list-card lw-data-card"
            >
                <div class="table-responsive-md lw-table-wrapper">
                    <table class="table table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte::adminlte.user_name') }}</th>
                                <th>{{ __('adminlte::adminlte.status') }}</th>
                                <th class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ __('adminlte::adminlte.Total') }}
                                </th>
                                <th>{{ __('adminlte::adminlte.updated_at') }}</th>
                                <th style="width:1%;white-space:nowrap;" class="text-center">
                                    {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($completedOrders as $o)
                            @php $total = $o->total_price ?? $o->total ?? null; @endphp
                            <tr>
                                <td class="text-muted">#{{ $o->id }}</td>
                                <td><strong>{{ $o->user->name ?? '-' }}</strong></td>
                                <td>
                                    <span class="badge lw-pill badge-success">
                                        <i class="fas fa-check small mr-1"></i>
                                        {{ $o->status_label ?? ucfirst($o->status) }}
                                    </span>
                                </td>
                                <td class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    <strong>{{ $money($total) }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($o->updated_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="lw-actions">
                                        @if(Route::has('orders.show'))
                                            <a href="{{ route('orders.show', $o->id) }}"
                                               class="btn btn-sm btn-outline-primary lw-action-btn"
                                               title="{{ __('adminlte::adminlte.details') }}"
                                            >
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline">
                                                    {{ __('adminlte::adminlte.details') }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    {{ __('adminlte::adminlte.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Users & Products --}}
    <div class="row">
        {{-- Newest 10 Users --}}
        <div class="col-12 col-xl-6 mb-3">
            <x-adminlte-card
                title="{{ __('adminlte::adminlte.newest_10_users') }}"
                theme="info"
                icon="fas fa-users"
                removable
                collapsible
                class="lw-list-card lw-data-card"
            >
                <div class="table-responsive-md lw-table-wrapper">
                    <table class="table table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte::adminlte.user_name') }}</th>
                                <th>{{ __('adminlte::adminlte.email') }}</th>
                                <th>{{ __('adminlte::adminlte.joined_at') }}</th>
                                <th style="width:1%;white-space:nowrap;" class="text-center">
                                    {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($newUsers as $u)
                            <tr>
                                <td class="text-muted">#{{ $u->id }}</td>
                                <td><strong>{{ $u->name }}</strong></td>
                                <td><small>{{ $u->email }}</small></td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($u->created_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="lw-actions">
                                        @if(Route::has('users.show'))
                                            <a href="{{ route('users.show', $u->id) }}"
                                               class="btn btn-sm btn-outline-primary lw-action-btn"
                                               title="{{ __('adminlte::adminlte.details') }}"
                                            >
                                                <i class="fas fa-user"></i>
                                                <span class="d-none d-md-inline">
                                                    {{ __('adminlte::adminlte.details') }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    {{ __('adminlte::adminlte.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Newest 10 Products --}}
        <div class="col-12 col-xl-6 mb-3">
            <x-adminlte-card
                title="{{ __('adminlte::adminlte.newest_10_products') ?? __('adminlte::adminlte.products') }}"
                theme="warning"
                icon="fas fa-box"
                removable
                collapsible
                class="lw-list-card lw-data-card"
            >
                <div class="table-responsive-md lw-table-wrapper">
                    <table class="table table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte::adminlte.name_en') }}</th>
                                <th>{{ __('adminlte::adminlte.name_ar') }}</th>
                                <th class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ __('adminlte::adminlte.price') }}
                                </th>
                                <th>{{ __('adminlte::adminlte.created_at') }}</th>
                                <th style="width:1%;white-space:nowrap;" class="text-center">
                                    {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($newProducts as $p)
                            <tr>
                                <td class="text-muted">#{{ $p->id }}</td>
                                <td><strong>{{ $p->name_en }}</strong></td>
                                <td>{{ $p->name_ar }}</td>
                                <td class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    <strong>{{ $money($p->price ?? null) }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($p->created_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="lw-actions">
                                        @if(Route::has('products.show'))
                                            <a href="{{ route('products.show', $p->id) }}"
                                               class="btn btn-sm btn-outline-primary lw-action-btn"
                                               title="{{ __('adminlte::adminlte.details') }}"
                                            >
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline">
                                                    {{ __('adminlte::adminlte.details') }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    {{ __('adminlte::adminlte.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Lightweight JS hooks --}}
    <script wire:ignore>
        window.addEventListener('show-details-modal', () => {
            const el = document.getElementById('detailsModal');
            if (!el) return;
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        });

        window.addEventListener('toast', (e) => {
            const { type = 'info', message = '' } = e.detail || {};
            if (message) { alert(message); }
        });
    </script>
</div>

@push('css')
<style>
    .lw-dashboard {
        padding-top: .75rem;
        padding-bottom: .75rem;
    }

    .lw-dashboard-pill-icon {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--brand-main, #2563eb), var(--brand-sub, #4f46e5));
        color: #fff;
        box-shadow: 0 4px 10px rgba(37, 99, 235, .35);
        font-size: .9rem;
    }

    .lw-dashboard-actions .lw-btn-soft {
        border-radius: 999px;
        padding-left: .9rem;
        padding-right: .9rem;
    }

    .lw-btn-pulse {
        position: relative;
        overflow: hidden;
    }
    .lw-btn-pulse::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        box-shadow: 0 0 0 0 rgba(34, 197, 94, .35);
        animation: lw-pulse 1.7s infinite;
    }
    @keyframes lw-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(34, 197, 94, .35); }
        70%  { box-shadow: 0 0 0 12px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    /* Stats cards */
    .lw-stat-row {
        row-gap: .75rem;
    }

    .lw-stat-card {
        display: flex;
        align-items: center;
        border-radius: 1rem;
        padding: .75rem .9rem;
        background: var(--lw-stat-bg, #0f172a);
        color: #fff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .35);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .06);
    }

    .lw-stat-card::before {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: radial-gradient(circle at center, rgba(255, 255, 255, .35), transparent 60%);
        opacity: .25;
        right: -40px;
        bottom: -40px;
    }

    .lw-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(15, 23, 42, .25);
        margin-{{ $isRtl ? 'left' : 'right' }}: .75rem;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .lw-stat-body {
        display: flex;
        flex-direction: column;
        gap: .1rem;
    }

    .lw-stat-label {
        font-size: .72rem;
        opacity: .85;
        white-space: normal;
    }

    .lw-stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        line-height: 1;
    }

    .lw-stat-primary {
        --lw-stat-bg: linear-gradient(135deg, var(--brand-main, #2563eb), #0ea5e9);
    }
    .lw-stat-success {
        --lw-stat-bg: linear-gradient(135deg, #16a34a, #22c55e);
    }
    .lw-stat-info {
        --lw-stat-bg: linear-gradient(135deg, #0ea5e9, #6366f1);
    }
    .lw-stat-warning {
        --lw-stat-bg: linear-gradient(135deg, #f97316, #facc15);
        color: #111827;
    }

    /* Cards & tables */
    .lw-data-card {
        border-radius: 1rem !important;
        border: 0 !important;
        box-shadow: 0 10px 25px rgba(15, 23, 42, .12);
    }

    .lw-data-card .card-header {
        border-bottom: 0;
        border-radius: 1rem 1rem 0 0 !important;
        padding-top: .75rem;
        padding-bottom: .75rem;
    }

    .lw-data-card .card-header .card-title {
        font-weight: 600;
        font-size: .95rem;
    }

    .lw-table-wrapper {
        border-radius: 0 0 1rem 1rem;
        overflow: hidden;
    }

    .lw-table {
        margin-bottom: 0;
        background-color: rgba(255, 255, 255, .98);
    }

    .lw-table thead {
        background: linear-gradient(90deg, rgba(15, 23, 42, .03), rgba(15, 23, 42, .06));
    }

    .lw-table thead th {
        border-bottom-width: 1px;
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #6b7280;
    }

    .lw-table tbody tr {
        transition: background-color .15s ease, transform .1s ease;
    }

    .lw-table tbody tr:hover {
        background-color: rgba(59, 130, 246, .03);
        transform: translateY(-1px);
    }

    .lw-table tbody td {
        font-size: .85rem;
        vertical-align: middle;
    }

    .lw-actions .lw-action-btn {
        border-radius: 999px;
        padding-left: .7rem;
        padding-right: .7rem;
        font-size: .75rem;
    }

    @media (max-width: 767.98px) {
        .lw-stat-card {
            padding: .6rem .7rem;
        }
        .lw-stat-icon {
            width: 34px;
            height: 34px;
            font-size: 1rem;
        }
        .lw-stat-value {
            font-size: 1rem;
        }
    }
</style>
@endpush
