<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderHistoryItem;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * When an order is being updated.
     */
    public function updating(Order $order): void
    {
        $this->snapshot($order);
    }

    /**
     * When an order is being deleted.
     */
    public function deleting(Order $order): void
    {
        $this->snapshot($order);
    }

    /**
     * Create a history record from the current order state.
     */
    protected function snapshot(Order $order): void
    {
        $original = $order->getOriginal();

        $history = OrderHistory::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id() ?? $order->user_id,
            'employee_id' => $original['employee_id'] ?? null,
            'status'      => $original['status'] ?? $order->status,
            'total_price' => $original['total_price'] ?? $order->total_price,
            'snapshot'    => [
                'order' => $original,
                'items' => $order->items()->get()->toArray(),
            ],
        ]);

        foreach ($order->items as $item) {
            OrderHistoryItem::create([
                'order_history_id' => $history->id,
                'product_id'       => $item->product_id,
                'color'            => $item->color,
                'quantity'         => $item->quantity,
                'price'            => $item->price,
                'total_price'      => $item->total_price,
            ]);
        }
    }
}
