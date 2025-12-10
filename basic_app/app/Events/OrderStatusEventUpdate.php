<?php

namespace App\Events;

use App\Models\Offer;
use App\Models\OrderStatus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusEventUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var array */
    public array $order_status;

    // If you want to force the connection regardless of .env:
    // public string $connection = 'pusher';

    public function __construct(OrderStatus $order_status)
    {
        // Prepare a clean payload
        $this->order_status = $order_status->toArray();
    }

    // Public channel (no auth)
    public function broadcastOn(): Channel
    {
        // MUST match the client subscription name
        return new Channel('orders_status');
    }

    // Client listens for this name
    public function broadcastAs(): string
    {
        return 'order_status_updated';
    }

    public function broadcastWith(): array
    {
        return ['order_status' => $this->order_status];
    }
}
