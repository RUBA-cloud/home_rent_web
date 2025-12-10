<?php

namespace App\Events;

use App\Models\Additonal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdditionalEventUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var array */
    public array $additonal;

    // If you want to force the connection regardless of .env:
    // public string $connection = 'pusher';

    public function __construct(Additonal $additional)
    {
        // Prepare a clean payload
        $this->additonal = $additional->toArray();
    }

    // Public channel (no auth)
    public function broadcastOn(): Channel
    {
        // MUST match the client subscription name
        return new Channel('additional');
    }

    // Client listens for this name
    public function broadcastAs(): string
    {
        return 'additional_updated';
    }

    public function broadcastWith(): array
    {
        return ['additional' => $this->additonal];
    }
}
