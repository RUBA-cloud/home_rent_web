<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class SizeHistory extends Model
{
  use HasFactory, Notifiable;
    //
    protected $table = 'sizes_history'; // Ensure the table name is correct

    protected $fillable = [
        'name_en',
        'name_ar',
        'descripation',
        'price',
        'is_active',
        'user_id',

    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
