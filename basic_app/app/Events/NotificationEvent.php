<?php
// app/Events/ChatNotificationCreated.php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NotificationEvent implements ShouldBroadcastNow
{
    public function __construct(
        public int $receiverId,
        public array $payload // {id, title, body, icon, link, created_at}
    ) {}

    public function broadcastOn(): array
    {
        return [ new PrivateChannel('notifications.user.' . $this->receiverId) ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
