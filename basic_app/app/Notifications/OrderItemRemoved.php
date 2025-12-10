<?php
// app/Notifications/OrderItemRemoved.php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderItemRemoved extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $productName,
        public string $note
    ) {}

    public function via($notifiable)
    {
        // Add 'mail', 'broadcast', or FCM channel class if configured
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type'       => 'order_item_removed',
            'order_id'   => $this->order->id,
            'product'    => $this->productName,
            'note'       => $this->note,
            'message'    => "An item ({$this->productName}) was removed from your order #{$this->order->id}.",
            'created_at' => now()->toIso8601String(),
        ];
    }
}
