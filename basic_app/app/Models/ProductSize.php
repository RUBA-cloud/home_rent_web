<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class ProductSize extends Model
{
    //
    use HasFactory, Notifiable;
    protected $table = 'product_size'; // Ensure the table name is correct
    protected $fillable = [
        'product_id',   // Foreign key to products table
        'size_id',      // Foreign key to sizes table
    ];
    protected $casts = ['created_at' => 'datetime', ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
