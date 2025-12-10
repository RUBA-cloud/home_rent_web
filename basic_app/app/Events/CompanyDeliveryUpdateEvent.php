<?php

namespace App\Events;

use App\Models\CompanyDelivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyDeliveryUpdateEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var array */
    public array $company_delivery;

    // If you want to force the connection regardless of .env:
    // public string $connection = 'pusher';

    public function __construct(CompanyDelivery $companyDelivery)
    {
        // Prepare a clean payload
        $this->company_delivery = $companyDelivery->toArray();
    }

    // Public channel (no auth)
    public function broadcastOn(): Channel
    {
        // MUST match the client subscription name
        return new Channel('company_delivery');
    }

    // Client listens for this name
    public function broadcastAs(): string
    {
        return 'company_delivery_updated';
    }

    public function broadcastWith(): array
    {
        return ['company_delivery_update' => $this->company_delivery];
    }
}
