<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryUpdateEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var array */
    public array $category;

    // If you want to force the connection regardless of .env:
    // public string $connection = 'pusher';

    public function __construct(Category $category)
    {
        // Prepare a clean payload
        $this->category = $category->toArray();
    }

    // Public channel (no auth)
    public function broadcastOn(): Channel
    {
        // MUST match the client subscription name
        return new Channel('categories');
    }

    // Client listens for this name
    public function broadcastAs(): string
    {
        return 'category_updated';
    }

    public function broadcastWith(): array
    {
        return ['category' => $this->category];
    }
}
