<?php

namespace App\Events;

use App\Models\CompanyDelivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyDeliveryEventUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The payload that will be broadcast to the client.
     *
     * @var array
     */
    public array $companyDelivery;

    // لو حابة تجبري الاتصال:
    // public string $connection = 'pusher';

    public function __construct(CompanyDelivery $companyDelivery)
    {
        // نحول الموديل لمصفوفة نظيفة ترسل للفرонт
        $this->companyDelivery = $companyDelivery->toArray();
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        // لازم يطابق اسم القناة اللي الكلاينت بيشترك فيها
        return new Channel('company_delivery');
    }

    /**
     * The event name the client will listen for.
     */
    public function broadcastAs(): string
    {
        return 'company_delivery_updated';
    }

    /**
     * The data to broadcast.
     */
    public function broadcastWith(): array
    {
        // هنا كانت المشكلة، استخدمنا اسم البرопيرتي الصحيح
        return [
            'company_delivery' => $this->companyDelivery,
        ];
    }
}
