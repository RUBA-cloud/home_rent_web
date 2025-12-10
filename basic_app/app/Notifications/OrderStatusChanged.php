<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $title, public string $body) {}

    public function via(object $notifiable): array
    {
        return ['fcm'];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->setNotification(
                FcmNotification::create()
                    ->setTitle($this->title)
                    ->setBody($this->body)
            )
            ->setAndroid(AndroidConfig::create()->setPriority('high'))
            ->setApns(ApnsConfig::create()->setPayload([
                'aps' => ['sound' => 'default', 'content-available' => 1],
            ]));
    }
}
