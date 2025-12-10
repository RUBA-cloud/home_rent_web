<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class ProductImageHistory extends Model
{
    //

    use HasFactory, Notifiable;
    protected $table = 'product_images_history'; // Ensure the table name is correct
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
        return $this->belongsTo(ProductHistory::class, 'product_id');
    }
}
