<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    //
    use HasFactory, Notifiable;
    protected $table = 'products'; // Ensure the table name is correct
    protected $fillable = [
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'price',
        'is_active',
        'user_id',
        'category_id',
        'product_type', // Assuming you have a product_type field
        'type_id', // Foreign key to types table
        'colors',
        'main_image',
        // Assuming you have a colors field, can be JSON or text
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'product_type' => 'string', // Assuming product_type is a string
        'type_id' => 'integer', // Assuming type_id is an integer
        'user_id' => 'integer',
        'colors' => 'array', // Assuming colors is stored as JSON
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_size', 'product_id', 'size_id');
    }
    public function additionals()
    {
        return $this->belongsToMany(Additonal::class, 'product_additional', 'product_id', 'additional_id');
    }
    public function type()
    {
    return $this->belongsTo(Type::class, 'type_id');
    }
    public function products(){
        return $this->belongsToMany(Category::class,'id','category_id');
    }
}
