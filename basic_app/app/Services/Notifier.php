<?php
// app/Services/Notifier.php
namespace App\Services;

use App\Models\AppNotification;

class Notifier
{
    /**
     * Send to a specific user
     */
    public static function toUser(int $userId, string $title, ?string $body = null, array $opts = []): AppNotification
    {
        return AppNotification::create([
            'user_id' => $userId,
            'title'   => $title,
            'body'    => $body,
            'type'    => $opts['type'] ?? null,
            'icon'    => $opts['icon'] ?? 'fas fa-bell',
            'link'    => $opts['link'] ?? null,
        ]);
    }

    /**
     * Broadcast to all users
     */
    public static function toAll(string $title, ?string $body = null, array $opts = []): AppNotification
    {
        return AppNotification::create([
            'user_id' => null,
            'title'   => $title,
            'body'    => $body,
            'type'    => $opts['type'] ?? null,
            'icon'    => $opts['icon'] ?? 'fas fa-bell',
            'link'    => $opts['link'] ?? null,
        ]);
    }
}
