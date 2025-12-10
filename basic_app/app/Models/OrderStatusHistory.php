<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class OrderStatusHistory extends Model
{
    //
    use HasFactory;
    use Notifiable;
    //
    protected $table = 'order-status-history';
     // Ensure the table name is correct
    protected $fillable = [
        'name_en',   // Foreign key to products table
        'name_ar',
        'is_active',
                'user_id'
         // Foreign key to additionals table
    ];
  public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
