<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffersTypeHistory extends Model
{
    //
    protected $table = 'offers_type'; // Ensure the table name is correct
    protected $fillable = [
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'is_active',
        'user_id',
        'is_discount',
        'is_total_gift',
        'is_total_discount',
        'discount_value_product',
        'discount_value_offer',
        'products_count_to_get_gift_offer',
        'type_id',
        'total_amount',
        'product_count_gift',
        // Foreign key to types table
        'category_id',
        'colors', // Assuming you have a colors field, can be JSON or text
        'offer_percentage', // Assuming you have an offer percentage field
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'integer',
        'category_id' => 'integer',
        'colors' => 'array', // Assuming colors is stored as JSON
        'offer_percentage' => 'float', // Assuming offer_percentage is a float
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
}
