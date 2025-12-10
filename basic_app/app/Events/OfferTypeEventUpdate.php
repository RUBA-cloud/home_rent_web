<?php

namespace App\Events;

use App\Models\Offer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferTypeEventUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var array */
    public array $offer_type;

    // If you want to force the connection regardless of .env:
    // public string $connection = 'pusher';

    public function __construct(Offer $offer_type)
    {
        // Prepare a clean payload
        $this->offer_type = $offer_type->toArray();
    }

    // Public channel (no auth)
    public function broadcastOn(): Channel
    {
        // MUST match the client subscription name
        return new Channel('offers_type');
    }

    // Client listens for this name
    public function broadcastAs(): string
    {
        return 'offer_type_updated';
    }

    public function broadcastWith(): array
    {
        return ['offer_type' => $this->offer_type];
    }
}
