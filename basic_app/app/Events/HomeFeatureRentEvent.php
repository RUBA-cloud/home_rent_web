<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;

use Illuminate\Broadcasting\Channel; // 👈 change this

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class HomeFeatureRentEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The company info payload.
     *
     * @var mixed
     */
    public $homeFeature;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $homeFeature
     * @return void
     */
    public function __construct($homeFeature)
    {
        $this->homeFeature = $homeFeature;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        // Public channel "home_feature"
        return new Channel('home_rent_feature');
    }

    /**
     * The event name to broadcast as.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'home_rent_feature_updated';
    }

    /**
     * Data to broadcast with the event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            // Frontend will receive: data.homeFeature
            'homeFeature' => $this->homeFeature,
        ];
    }
}
