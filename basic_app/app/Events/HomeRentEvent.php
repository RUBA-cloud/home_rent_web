<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;

use Illuminate\Broadcasting\Channel; // 👈 change this

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class HomeRentEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The company info payload.
     *
     * @var mixed
     */
    public $homeRent;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $homeRent
     * @return void
     */
    public function __construct($homeRent)
    {
        $this->homeRent = $homeRent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        // Public channel "home_rent"
        return new Channel('home_rent');
    }

    /**
     * The event name to broadcast as.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'home_rent_updated';
    }

    /**
     * Data to broadcast with the event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            // Frontend will receive: data.homeRent
            'homeRent' => $this->homeRent,
        ];
    }
}
