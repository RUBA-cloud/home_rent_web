<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeEventUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var array */
    public array $employee;

    // If you want to force the connection regardless of .env:
    // public string $connection = 'pusher';

    public function __construct(User $employee)
    {
        // Prepare a clean payload
        $this->employee = $employee->toArray();
    }

    // Public channel (no auth)
    public function broadcastOn(): Channel
    {
        // MUST match the client subscription name
        return new Channel('employees');
    }

    // Client listens for this name
    public function broadcastAs(): string
    {
        return 'employee_updated';
    }

    public function broadcastWith(): array
    {
        return ['company' => $this->employee];
    }
}
