<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    use HasFactory;

    // Optional: specify table name if it's not the plural of the model
    protected $table = 'booking_table';

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'home_id',
        'from_date',
        'end_date',
        'adults_count',
        'children_count',
    ];

    // Cast date fields to Carbon instances
    protected $dates = [
        'from_date',
        'end_date',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function home()
    {
        return $this->belongsTo(HomeRent::class, 'home_id');
    }
}
