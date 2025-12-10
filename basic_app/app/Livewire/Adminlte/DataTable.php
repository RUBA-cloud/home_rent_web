<?php

namespace App\Livewire\Adminlte;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    /** Livewire expects this to be public */
    public string $paginationTheme = 'bootstrap';

    /** Passed-in props */
    public array  $fields = [];        // [['key'=>'name','label'=>'Name','type'=>null], ...]
    public string $model;              // FQCN, e.g. App\Models\User

    public ?string $detailsRoute  = null;
    public ?string $editRoute     = null;
    public ?string $deleteRoute   = null;
    public ?string $reactiveRoute = null;
    public ?string $detailsView   = null;
    public ?string $initialRoute  = null;

    /** Behavior */
    public array  $searchIn = [];      // e.g. ['name','email','user.name']
    public int    $perPage  = 12;
    public string $orderBy  = 'id';
    public string $orderDir = 'desc';

    /** Where to show pagination: in table row (true) */
    public bool $paginationInTable = true;

    /** State */
    public string  $search = '';
    public ?string $initialRouteUrl = null;
    public ?string $detailsHtml     = null;

    /**
     * Accept both camelCase and kebab/snake for convenience.
     */
    public function mount(
        array $fields,
        string $model,

        // Route props (both forms accepted)
        ?string $detailsRoute = null,
        ?string $details_route = null,

        ?string $editRoute = null,
        ?string $edit_route = null,

        ?string $deleteRoute = null,
        ?string $delete_route = null,

        ?string $reactiveRoute = null,
        ?string $reactive_route = null,

        ?string $detailsView = null,
        ?string $details_view = null,

        ?string $initialRoute = null,
        ?string $initial_route = null,

        // Search / paging (both forms accepted)
        ?array $searchIn = null,
        ?array $search_in = null,

        ?int $perPage = null,
        ?int $per_page = null,

        // Pagination placement (optional external control)
        ?bool $paginationInTable = True,
        ?bool $pagination_in_table = TRUE,
    ): void {
        $this->fields = $fields;
        $this->model  = $model;

        // Normalize to camelCase public properties
        $this->detailsRoute  = $detailsRoute  ?? $details_route  ?? $this->detailsRoute;
        $this->editRoute     = $editRoute     ?? $edit_route     ?? $this->editRoute;
        $this->deleteRoute   = $deleteRoute   ?? $delete_route   ?? $this->deleteRoute;
        $this->reactiveRoute = $reactiveRoute ?? $reactive_route ?? $this->reactiveRoute;
        $this->detailsView   = $detailsView   ?? $details_view   ?? $this->detailsView;
        $this->initialRoute  = $initialRoute  ?? $initial_route  ?? $this->initialRoute;

        // Search fields
        $this->searchIn = $searchIn ?? $search_in ?? $this->searchIn ?? [];

        // Per-page
        $this->perPage = $perPage ?? $per_page ?? $this->perPage;

        // Pagination placement (default true = inside table row)
        $this->paginationInTable = $paginationInTable
            ?? $pagination_in_table
            ?? $this->paginationInTable;

        // Resolve initial route to absolute URL (accept route name OR absolute/relative URL)
        if ($this->initialRoute) {
            $this->initialRouteUrl = Str::startsWith($this->initialRoute, ['http://','https://','/'])
                ? $this->initialRoute
                : route($this->initialRoute);
        }
    }

    /** Convenience accessor for Blade */
    public function getHasDetailsRouteProperty(): bool
    {
        return filled($this->detailsRoute);
    }

    /** Reset to first page when search changes */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        /** @var Builder $q */
        $q = ($this->model)::query();

        if (filled($this->search) && !empty($this->searchIn)) {
            $term = $this->search;

            // Search in plain columns and related columns (e.g., user.name)
            $q->where(function (Builder $w) use ($term) {
                foreach ($this->searchIn as $col) {
                    if (str_contains($col, '.')) {
                        [$rel, $relCol] = explode('.', $col, 2);
                        $w->orWhereHas($rel, function (Builder $qr) use ($relCol, $term) {
                            $qr->where($relCol, 'like', "%{$term}%");
                        });
                    } else {
                        $w->orWhere($col, 'like', "%{$term}%");
                    }
                }
            });
        }

        $relations = $this->relationsToEagerLoad();

        $collection = $q->with($relations)
            ->orderBy($this->orderBy, $this->orderDir)
            ->paginate(4)
            ->withQueryString();

        return view('livewire.adminlte.data-table', [
            'rows'              => $collection,
            'paginationInTable' => $this->paginationInTable,
        ]);
    }

    /**
     * Eager-load relations found in:
     *  - dotted field keys (e.g., "user.name" in $fields)
     *  - dotted searchIn entries (e.g., "user.name" in $searchIn)
     */
    protected function relationsToEagerLoad(): array
    {
        $rels = [];

        // From fields
        foreach ($this->fields as $f) {
            $key = $f['key'] ?? '';
            if (is_string($key) && $key !== '' && str_contains($key, '.')) {
                $rels[] = Str::before($key, '.');
            }
        }

        // From searchIn
        foreach ($this->searchIn as $k) {
            if (is_string($k) && str_contains($k, '.')) {
                $rels[] = Str::before($k, '.');
            }
        }

        // Keep only relations that actually exist on the model
        $rels = array_values(array_unique(array_filter($rels, function ($rel) {
            return method_exists($this->model, $rel);
        })));

        return $rels;
    }

    /** Delete via Livewire (if you don't want to post to route) */
    public function delete(int $id): void
    {
        $model = ($this->model)::findOrFail($id);
        $model->delete();

        $this->dispatch('toast', type: 'success', message: __('adminlte::adminlte.delete'));
        $this->resetPage();
    }

    /** Reactivate (toggle is_active to true) via Livewire */
    public function reactivate(int $id): void
    {
        $model = ($this->model)::findOrFail($id);
        if (!is_null($model->is_active)) {
            $model->is_active = true;
            $model->save();
            $this->dispatch('toast', type: 'success', message: __('Reactivated.'));
            $this->resetPage();
        }
    }

    /** Load details (render a view or fallback to pretty JSON) and open modal */
    public function details(int $id): void
    {
        $item = ($this->model)::with($this->relationsToEagerLoad())->findOrFail($id);

        if ($this->detailsView) {
            $this->detailsHtml = view($this->detailsView, compact('item'))->render();
        } else {
            $this->detailsHtml = '<pre class="mb-0">'.e(json_encode($item->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)).'</pre>';
        }

        $this->dispatch('show-details-modal');
    }

    /** Optional hard reload to the initial route URL */
    public function reloadToInitialRoute()
    {
        if ($this->initialRouteUrl) {
            return redirect()->to($this->initialRouteUrl);
        }
    }

    /** Helper for Blade to resolve dotted keys */
    public function resolveValue($item, string $key)
    {
        $segments = explode('.', $key);
        $data = $item;

        foreach ($segments as $seg) {
            if (is_array($data)) {
                $data = $data[$seg] ?? null;
            } elseif (is_object($data)) {
                $data = $data->{$seg} ?? null;
            } else {
                return null;
            }
        }
        return $data;
    }
}
