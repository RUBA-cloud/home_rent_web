<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $token = app('auth.password.broker')->createToken($notifiable);
        // Generate the reset URL using the token and email
     $resetUrl = url(route('password.reset', [
        'token' => $token,
        'email' => $notifiable->getEmailForPasswordReset(),
    ], false));
        return (new MailMessage)
            ->subject('Reset Your Password - Coffee Beans')

            ->line('You are receiving this email because we received a password reset request for your account.')
            ->view('Auth.forgetpassword',['url'=> $resetUrl, 'user' => $notifiable])

            ->line('If you did not request a password reset, no further action is required.');

    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
