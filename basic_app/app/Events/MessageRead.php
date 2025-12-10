<?php
// app/Events/MessagesRead.php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageRead implements ShouldBroadcastNow
{
    /**
     * @param int $readerId     The user who just read
     * @param int $senderId     The counterpart (who will receive this event)
     * @param array<int> $messageIds The messages that were marked read
     */
    public function __construct(
        public int $readerId,
        public int $senderId,
        public array $messageIds
    ) {}

    public function broadcastOn(): array
    {
        // Notify the sender that their messages were read
        return [ new PrivateChannel('chat.user.' . $this->senderId) ];
    }

    public function broadcastAs(): string
    {
        return 'messages.read';
    }

    public function broadcastWith(): array
    {
        return [
            'reader_id'   => $this->readerId,
            'message_ids' => $this->messageIds,
        ];
    }
}
