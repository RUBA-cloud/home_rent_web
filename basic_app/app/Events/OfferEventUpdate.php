<?php

namespace App\Events;

use App\Models\Offer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferEventUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var array */
    public array $offer;

    // If you want to force the connection regardless of .env:
    // public string $connection = 'pusher';

    public function __construct(Offer $offer)
    {
        // Prepare a clean payload
        $this->offer = $offer->toArray();
    }

    // Public channel (no auth)
    public function broadcastOn(): Channel
    {
        // MUST match the client subscription name
        return new Channel('offers');
    }

    // Client listens for this name
    public function broadcastAs(): string
    {
        return 'offer_updated';
    }

    public function broadcastWith(): array
    {
        return ['offer' => $this->offer];
    }
}
