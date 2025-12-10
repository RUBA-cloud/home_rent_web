<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyDeliveryRequest;
use App\Models\CompanyDelivery;
use App\Models\CompanyDeliveryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanyDeliveryController extends Controller
{
    /**
     * List deliveries.
     */
    public function index()
    {
        $company_delivery = CompanyDelivery::with('user')
            ->orderByDesc('id')
            ->paginate(5);

        return view('company_delivery.index', compact('company_delivery'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('company_delivery.create');
    }

    /**
     * Search by name/active.
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $activeFilter = $request->has('active')
            ? filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN)
            : null;

        $company_delivery = CompanyDelivery::query()
            ->when($q !== '', function ($qbuilder) use ($q) {
                $qbuilder->where(function ($w) use ($q) {
                    $w->where('name_en', 'like', "%{$q}%")
                      ->orWhere('name_ar', 'like', "%{$q}%");
                });
            })
            ->when(!is_null($activeFilter), function ($qbuilder) use ($activeFilter) {
                $qbuilder->where('is_active', (bool) $activeFilter);
            })
            ->with('user')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('company_delivery.index', compact('company_delivery', 'q', 'activeFilter'));
    }

    /**
     * Store.
     */
    public function store(CompanyDeliveryRequest $request)
    {
        $data = $request->validated();
        $data['user_id']   = $data['user_id'] ?? Auth::id();
        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        DB::transaction(function () use ($data) {
            $delivery = CompanyDelivery::create($data);
            $this->writeHistory($delivery, 'created', $delivery->toArray());
        });

        return redirect()
            ->route('company_delivery.index')
            ->with('success', __('Company delivery created successfully.'));
    }

    /**
     * Show.
     */
    public function show(CompanyDelivery $company_delivery)
    {
        return view('company_delivery.show', compact('company_delivery'));
    }

    /**
     * Edit form.
     */
    public function edit(CompanyDelivery $companyDelivery)
    {
        return view('company_delivery.edit', compact('companyDelivery'));
    }

    /**
     * Update (log old/new to history).
     */
    public function update(CompanyDeliveryRequest $request, CompanyDelivery $companyDelivery)
    {
        $data = $request->validated();
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        DB::transaction(function () use ($companyDelivery, $data) {
            // snapshot BEFORE
            $this->writeHistory($companyDelivery, 'updated_before', $companyDelivery->toArray());

            // apply updates
            $companyDelivery->update($data);
broadcast(new \App\Events\CompanyDeliveryEventUpdate($companyDelivery))->toOthers();
            // snapshot AFTER
            $this->writeHistory($companyDelivery, 'updated_after', $companyDelivery->fresh()->toArray());
        });

        return redirect()
            ->route('company_delivery.index')
            ->with('success', __('Company delivery updated successfully.'));
    }

    /**
     * "Delete": archive in history + set inactive (soft deactivate).
     */
    public function destroy(CompanyDelivery $companyDelivery)
    {
        DB::transaction(function () use ($companyDelivery) {
            $this->writeHistory($companyDelivery, 'deleted', $companyDelivery->toArray());
            $companyDelivery->update(['is_active' => false]);
        });

        return redirect()
            ->route('company_delivery.index')
            ->with('success', __('Company delivery deactivated and archived to history.'));
    }

    /**
     * History list.
     */
    public function history()
    {
        $history = CompanyDeliveryHistory::with('user')
            ->orderByDesc('id')
            ->paginate(20);

        return view('company_delivery.history', compact('history'));
    }

    /**
     * Restore from a specific history row and set active.
     */
    public function restore(CompanyDeliveryHistory $history)
    {
        DB::transaction(function () use ($history) {
            // Find source record by FK if present
            $delivery = null;

            $historyTable = (new CompanyDeliveryHistory())->getTable();
            if (Schema::hasColumn($historyTable, 'company_delivery_id') && !empty($history->company_delivery_id)) {
                $delivery = CompanyDelivery::find($history->company_delivery_id);
            }

            // Fallback: match on names
            if (!$delivery) {
                $delivery = CompanyDelivery::where('name_en', $history->name_en)
                    ->where('name_ar', $history->name_ar)
                    ->first();
            }

            $payload = [
                'name_en'   => $history->name_en,
                'name_ar'   => $history->name_ar,
                'is_active' => true,
                'user_id'   => $history->user_id ?? Auth::id(),
            ];

            if ($delivery) {
                $this->writeHistory($delivery, 'restored_before', $delivery->toArray());
                $delivery->update($payload);
                $this->writeHistory($delivery, 'restored_after', $delivery->fresh()->toArray());
            } else {
                $delivery = CompanyDelivery::create($payload);
                $this->writeHistory($delivery, 'restored_created', $delivery->toArray());
            }
        });

        return redirect()
            ->route('company_delivery.index')
            ->with('success', __('Company delivery restored and activated from history.'));
    }

    /**
     * Write a history row. Adds optional columns only if they exist.
     */
    protected function writeHistory(CompanyDelivery $delivery, string $action, array $snapshot = []): void
    {
        $payload = [
            'name_en'   => $snapshot['name_en']   ?? $delivery->name_en,
            'name_ar'   => $snapshot['name_ar']   ?? $delivery->name_ar,
            'is_active' => $snapshot['is_active'] ?? $delivery->is_active,
            'user_id'   => $snapshot['user_id']   ?? $delivery->user_id,
        ];

        $historyTable = (new CompanyDeliveryHistory())->getTable();

        // include FK if your table has it
        if (Schema::hasColumn($historyTable, 'company_delivery_id')) {
            $payload['company_delivery_id'] = $delivery->id;
        }

        // include action if your table has it
        if (Schema::hasColumn($historyTable, 'action')) {
            $payload['action'] = $action;
        }

        CompanyDeliveryHistory::create($payload);
    }
}
