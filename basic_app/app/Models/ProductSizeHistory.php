<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class ProductSizeHistory extends Model
{
    //
    use HasFactory, Notifiable;
    protected $table = 'product_size_history'; // Ensure the table name is correct
    protected $fillable = [
        'product_id',   // Foreign key to products table
        'size_id',
          // Foreign key to sizes table
    ];

    public function product()
    {
        return $this->belongsTo(ProductHistory::class, 'product_id');
    }
}
