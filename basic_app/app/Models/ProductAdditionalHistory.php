<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAdditionalHistory extends Model
{
    protected $table = 'product_additional_history'; // Ensure the table name is correct
    protected $fillable = [
        'product_history_id	',   // Foreign key to products table
        'additional_id', // Foreign key to additionals table
    ];
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
    public function product()
    {
        return $this->belongsTo(ProducHistory::class, 'product_history_id');
    }
}

