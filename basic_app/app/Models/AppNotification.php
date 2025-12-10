<?php // app/Models/AppNotification.php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'notifications_custom';

    protected $fillable = [
        'user_id','title','body','type','icon','link','read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    /** Unread for a user (or global+user) */
    public function scopeForUser(Builder $q, ?int $userId): Builder {
        return $q->where(function($qq) use ($userId) {
            $qq->whereNull('user_id');
            if ($userId) $qq->orWhere('user_id', $userId);
        });
    }

    public function scopeUnread(Builder $q): Builder {
        return $q->whereNull('read_at');
    }
}
