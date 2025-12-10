<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Http\Requests\OrderStatusRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderStatusController extends Controller
{
    /** List order statuses */
    public function index()
    {
        $order_statuses = OrderStatus::with('user')
            ->orderByDesc('id')
            ->paginate(5);

        return view('order_status.index', compact('order_statuses'));
    }

    /** History list (all order statuses) */
    public function history()
    {
        $history = OrderStatusHistory::with('user')
            ->orderByDesc('id')
            ->paginate(20);

        // Use a single, consistent variable name in the view
        return view('order_status.history', compact('history'));
    }

    /** Show create form */
    public function create()
    {
        return view('order_status.create');
    }

    /** Search by name/active */
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $activeFilter = $request->has('active')
            ? filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN)
            : null;

        $order_statuses = OrderStatus::query()
            ->when($q !== '', function ($qbuilder) use ($q) {
                $qbuilder->where(function ($w) use ($q) {
                    $w->where('name_en', 'like', "%{$q}%")
                      ->orWhere('name_ar', 'like', "%{$q}%");
                });
            })
            ->when(!is_null($activeFilter), fn ($qb) => $qb->where('is_active', $activeFilter))
            ->with('user')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('order_status.index', compact('order_statuses', 'q', 'activeFilter'));
    }

    /** Store */
    public function store(OrderStatusRequest $request)
    {
        $data = $this->validatePayload($request);
        $data['user_id']   = $data['user_id']   ?? Auth::id();
        $data['is_active'] = array_key_exists('is_active', $data) ? (bool) $data['is_active'] : true;

        DB::transaction(function () use ($data) {
            $status = OrderStatus::create($data);
            $this->writeHistory($status, 'created', $status->toArray());
        });

        return redirect()->route('order_status.index')
            ->with('success', __('Order status created successfully.'));
    }

    /** Show */
    public function show(OrderStatus $order_status)
    {
        return view('order_status.show', compact('order_status'));
    }

    /** Edit form */
    public function edit(OrderStatus $order_status)
    {
        return view('order_status.edit', compact('order_status'));
    }

    /** Update (log old/new) */
    public function update(OrderSatausRequest $request, OrderStatus $order_status)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($order_status, $data) {
            $this->writeHistory($order_status, 'updated_before', $order_status->toArray());
            $order_status->update($data);
            broadcast(new \App\Events\OrderStatusEventUpdate($order_status))->toOthers();
            $this->writeHistory($order_status, 'updated_after', $order_status->fresh()->toArray());
        });

        return redirect()->route('order_status.index')
            ->with('success', __('Order status updated successfully.'));
    }

    /** "Delete": archive + deactivate (soft) */
    public function destroy(OrderStatus $order_status)
    {
        DB::transaction(function () use ($order_status) {
            $this->writeHistory($order_status, 'deleted', $order_status->toArray());
            $order_status->update(['is_active' => false]);
        });

        return redirect()->route('order_status.index')
            ->with('success', __('Order status deactivated and archived to history.'));
    }

    /** Restore from a specific history row and set active */
    public function restore(OrderStatusHistory $history)
    {
        DB::transaction(function () use ($history) {
            $historyTable = (new OrderStatusHistory())->getTable();

            // Prefer FK if present
            $status = null;
            if (Schema::hasColumn($historyTable, 'order_status_id') && !empty($history->order_status_id)) {
                $status = OrderStatus::find($history->order_status_id);
            }

            // Fallback if no FK
            if (!$status) {
                $status = OrderStatus::where('name_en', $history->name_en ?? null)
                                     ->where('name_ar', $history->name_ar ?? null)
                                     ->first();
            }

            $payload = [
                'name_en'   => $history->name_en,
                'name_ar'   => $history->name_ar,
                'is_active' => true,
                'user_id'   => $history->user_id,
            ];

            if ($status) {
                $this->writeHistory($status, 'restored_before', $status->toArray());
                $status->update($payload);
                $this->writeHistory($status, 'restored_after', $status->fresh()->toArray());
            } else {
                $status = OrderStatus::create($payload);
                $this->writeHistory($status, 'restored_created', $status->toArray());
            }
        });

        return redirect()->route('order_status.index')
            ->with('success', __('Order status restored and activated from history.'));
    }

    /** Write a history row (adds optional columns only if they exist) */
    protected function writeHistory(OrderStatus $status, string $action, array $snapshot = []): void
    {
        $historyTable = (new OrderStatusHistory())->getTable();

        $payload = [
            'name_en'   => $snapshot['name_en']   ?? $status->name_en,
            'name_ar'   => $snapshot['name_ar']   ?? $status->name_ar,
            'is_active' => $snapshot['is_active'] ?? $status->is_active,
            'user_id'   => $snapshot['user_id']   ?? $status->user_id,
        ];

        if (Schema::hasColumn($historyTable, 'order_status_id')) {
            $payload['order_status_id'] = $status->id;
        }
        if (Schema::hasColumn($historyTable, 'action')) {
            $payload['action'] = $action;
        }

        OrderStatusHistory::create($payload);
    }
}
