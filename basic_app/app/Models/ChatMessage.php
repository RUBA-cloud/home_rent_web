<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use SoftDeletes;

    protected $table = "messages";
    // Fillable attributes
    protected $fillable = [
        'message',
        'sender_id',
        'receiver_id', // <-- note the correct spelling
         'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /** The user who sent the message */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /** The user who receives the message */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    // app/Models/User.php
public function sentMessages(){ return $this->hasMany(\App\Models\ChatMessage::class, 'sender_id'); }
public function receiverMessage(): BelongsTo
{
    return $this->belongsTo(User::class, 'receiver_id');
}
}
