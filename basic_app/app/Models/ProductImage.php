<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class ProductImage extends Model
{
    //

    use HasFactory, Notifiable;
    protected $table = 'product_images'; // Ensure the table name is correct
    protected $fillable = [
        'product_id',
        'image_path', // or just 'image' if you prefer
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
