{{-- ONE root element only --}}
<div class="card-body" wire:poll.10s>
    @php
        $isRtl = app()->getLocale() === 'ar';
    @endphp

    <x-adminlte-card class="lw-list-card">
        {{-- Toolbar: search + small summary --}}
        <div class="lw-toolbar">
            <div class="lw-search-group input-group">
                <input type="text"
                       class="form-control"
                       placeholder="{{ __('adminlte::adminlte.search') }}"
                       wire:model.debounce.300ms="search">
                <button class="btn btn-primary btn-refresh"
                        type="button"
                        wire:click="$refresh"
                        title="{{ __('adminlte::adminlte.refresh') ?? 'Refresh' }}">
                    <i class="fas fa-sync"></i>
                </button>
            </div>

            @if(method_exists($rows, 'total'))
                <div class="lw-summary {{ $isRtl ? 'text-left' : 'text-right' }}">
                    {{ __('adminlte::adminlte.total') ?? 'Total' }}:
                    <strong>{{ $rows->total() }}</strong>
                </div>
            @endif
        </div>

        {{-- Table --}}
        <div class="table-responsive-md lw-table-wrapper">
            <table
                dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
                class="table table-bordered table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                <thead>
                    <tr dir="{{ $isRtl ? 'rtl' : 'ltr' }}" style="align-content: center">
                        <th style="width:60px;">#</th>
                        @foreach ($fields as $field)
                            <th>
                                {{ $field['label'] ?? ucfirst(str_replace('_',' ', $field['key'] ?? '')) }}
                            </th>
                        @endforeach
                        <th style="width:1%;white-space:nowrap;" class="{{ $isRtl ? 'text-left' : 'text-right' }}">
                            {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $firstItem   = method_exists($rows, 'firstItem') ? ($rows->firstItem() ?? 1) : 1;
                    $routeParamName = $routeParamName ?? 'id';
                @endphp

                @forelse ($rows as $row)
                    <tr wire:key="row-{{ $row->id }}" class="lw-row">
                        {{-- Row index --}}
                        <td>
                            <span class="badge badge-light lw-pill">
                                {{ $loop->iteration + ($firstItem - 1) }}
                            </span>
                        </td>

                        {{-- Dynamic fields --}}
                        @foreach ($fields as $field)
                            @php
                                $key  = $field['key'] ?? '';
                                $type = $field['type'] ?? null;
                                $data = $this->resolveValue($row, $key);
                            @endphp
                            <td>
                                @switch($type)
                                    @case('bool')
                                        <span class="badge lw-pill {{ $data ? 'bg-success' : 'bg-danger' }}">
                                            {{ $data ? __('adminlte::adminlte.yes') : __('adminlte::adminlte.no') }}
                                        </span>
                                        @break

                                    @case('color')
                                        <span class="color-swatch-24"
                                              title="{{ $data }}"
                                              style="background: {{ $data }}"></span>
                                        @break

                                    @case('image')
                                        @if ($data)
                                            <img class="img-thumb-40" width="50" height="50"
                                                 src="{{ \Illuminate\Support\Str::startsWith($data, ['http://','https://'])
                                                        ? $data
                                                        : asset('storage/'.ltrim((string)$data,'/')) }}"
                                                 alt="image">
                                        @else
                                            <span class="text-muted">
                                                {{ __('adminlte::adminlte.no_image') }}
                                            </span>
                                        @endif
                                        @break

                                    @case('status')
                                        @php
                                            $status = (int) ($data ?? 0);
                                            $labels = [
                                                0 => __('adminlte::adminlte.pending')   ?: 'Pending',
                                                1 => __('adminlte::adminlte.accepted')  ?: 'Accepted',
                                                2 => __('adminlte::adminlte.rejected')  ?: 'Rejected',
                                                3 => __('adminlte::adminlte.completed') ?: 'Completed',
                                            ];
                                            $classes = [
                                                0 => 'bg-secondary',
                                                1 => 'bg-success',
                                                2 => 'bg-danger',
                                                3 => 'bg-primary',
                                            ];
                                            $label  = $labels[$status]  ?? __('adminlte::adminlte.unknown') ?: 'Unknown';
                                            $class  = $classes[$status] ?? 'bg-light text-dark';
                                        @endphp
                                        <span class="badge lw-pill {{ $class }}">{{ $label }}</span>
                                        @break

                                    @default
                                        {{ is_scalar($data) ? $data : (is_null($data) ? '' : json_encode($data, JSON_UNESCAPED_UNICODE)) }}
                                @endswitch
                            </td>
                        @endforeach

                        {{-- Actions --}}
                        <td class="{{ $isRtl ? 'text-left' : 'text-right' }}">
                            <div class="lw-actions">
                                {{-- Details --}}
                                @if(!empty($detailsRoute))
                                    <a class="btn btn-info btn-sm lw-action-btn"
                                       style="background:gray;color:white"
                                       href="{{ route($detailsRoute, $row->id) }}">
                                        <i class="fas fa-eye"></i>
                                        {{ __('adminlte::adminlte.details') ?: 'Details' }}
                                    </a>
                                @else
                                    <button type="button"
                                            class="btn btn-info btn-sm lw-action-btn"
                                            wire:click="details({{ $row->id }})">
                                        <i class="fas fa-eye"></i>
                                        {{ __('adminlte::adminlte.details') ?: 'Details' }}
                                    </button>
                                @endif

                                {{-- Edit --}}
                                @if(!empty($editRoute))
                                    <a class="btn btn-success btn-sm lw-action-btn"
                                       style="background:green;color:white"
                                       href="{{ route($editRoute, $row->id) }}">
                                        <i class="fas fa-edit"></i>
                                        {{ __('adminlte::adminlte.edit') ?: 'Edit' }}
                                    </a>
                                @endif

                                @php $isActiveRow = data_get($row, 'is_active', true); @endphp

                                {{-- Delete / Reactivate --}}
                                @if($isActiveRow)
                                    @if(!empty($deleteRoute))
                                        <form action="{{ route($deleteRoute, $row->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm(@json(__('adminlte::adminlte.are_you_sure_youـwant_to_delete') ?: 'Delete?'))">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm lw-action-btn">
                                                <i class="fas fa-trash"></i>
                                                {{ __('adminlte::adminlte.delete') ?: 'Delete' }}
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-danger btn-sm lw-action-btn"
                                                wire:click="delete({{ $row->id }})"
                                                onclick="return confirm(@json(__('adminlte::adminlte.are_you_sure_youـwant_to_delete') ?: 'Delete?'))">
                                            <i class="fas fa-trash"></i>
                                            {{ __('adminlte::adminlte.delete') ?: 'Delete' }}
                                        </button>
                                    @endif
                                @else
                                    @if(!empty($reactiveRoute))
                                        <form action="{{ route($reactiveRoute, $row->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm(@json(__('adminlte::adminlte.do_you_want_to_reactive') ?: 'Reactivate?'))">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-warning btn-sm lw-action-btn" style="background:green;color:white">
                                                <i class="fas fa-undo"></i>
                                                {{ __('adminlte::adminlte.reactive') ?: 'Reactivate' }}
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-warning btn-sm lw-action-btn"
                                                wire:click="reactivate({{ $row->id }})"
                                                onclick="return confirm(@json(__('adminlte::adminlte.do_you_want_to_reactive') ?: 'Reactivate?'))">
                                            <i class="fas fa-undo"></i>
                                            {{ __('adminlte::adminlte.reactive') ?: 'Reactivate' }}
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($fields) + 2 }}" class="text-center text-muted">
                            {{ __('adminlte::adminlte.no_data_found') ?: 'No data found' }}
                        </td>
                    </tr>
                @endforelse

                {{-- Pagination INSIDE table as a full-width row --}}
                @if (method_exists($rows, 'hasPages') && $rows->hasPages())
                    <tr>
                        <td colspan="{{ count($fields) + 2 }}">
                            <div class="mt-2 d-flex {{ $isRtl ? 'justify-content-start' : 'justify-content-end' }}">
                                {{ $rows->links('pagination::bootstrap-4') }}
                            </div>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </x-adminlte-card>

    {{-- Lightweight script hooks --}}
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
