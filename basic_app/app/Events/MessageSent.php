<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // instant push
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    public function __construct(ChatMessage $message)
    {
        // Preload relationships so Pusher payload includes sender/receiver info
        $this->message = $message->loadMissing([
            'sender:id,name,avatar_path',
            'receiver:id,name,avatar_path',
        ]);
    }

    /**
     * Broadcast on *both* sender & receiver private channels.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel ('chat.user.' . $this->message->receiver_id),
            new Channel('chat.user.' . $this->message->sender_id),
        ];
    }

    /**
     * The event name that the frontend listens to.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * The data sent to the client.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id'          => $this->message->id,
                'message'     => $this->message->message,
                'sender_id'   => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'created_at'  => optional($this->message->created_at)->toIso8601String(),

                // Include sender/receiver details for UI rendering
                'sender' => [
                    'id'          => $this->message->sender->id,
                    'name'        => $this->message->sender->name,
                    'avatar_path' => $this->message->sender->avatar_path,
                ],
                'receiver' => [
                    'id'          => $this->message->receiver->id,
                    'name'        => $this->message->receiver->name,
                    'avatar_path' => $this->message->receiver->avatar_path,
                ],
            ],
        ];
    }

    // Optional if you want async:
    // public string $broadcastQueue = 'broadcasts';
}
