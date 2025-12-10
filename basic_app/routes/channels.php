<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('private.company.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('private.company.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('company_info', function($user,$id){ return $user!==null;});
Broadcast::channel('chat.user.{userId}', fn($user, $userId) => (int)$user->id === (int)$userId);
Broadcast::channel('notifications.user.{userId}', function ($user, int $userId) {
    return (int) $user->id === (int) $userId;
});
