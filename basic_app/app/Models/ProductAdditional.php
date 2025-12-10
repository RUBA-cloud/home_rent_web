<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class ProductAdditional extends Model
{
    use HasFactory;
    use Notifiable;
    //
    protected $table = 'product_additional'; // Ensure the table name is correct
    protected $fillable = [
        'product_id',   // Foreign key to products table
        'additional_id', // Foreign key to additionals table
    ];
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
