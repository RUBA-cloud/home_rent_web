<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;

use Illuminate\Broadcasting\Channel; // 👈 change this

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class CompanyInfoEventSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The company info payload.
     *
     * @var mixed
     */
    public $companyInfo;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $companyInfo
     * @return void
     */
    public function __construct($companyInfo)
    {
        $this->companyInfo = $companyInfo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        // Public channel "company_info"
        return new Channel('company_info');
    }

    /**
     * The event name to broadcast as.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'company_info_updated';
    }

    /**
     * Data to broadcast with the event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            // Frontend will receive: data.company
            'company' => $this->companyInfo,
        ];
    }
}
