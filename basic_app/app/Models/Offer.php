<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    //
    protected $table = 'offers'; // Ensure the table name is correct
    protected $fillable = [
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'price',
        'is_active',
        'user_id',
        'category_ids',
        'start_date',
        'end_date',
        'type_id', // Foreign key to types table
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'category_ids' => 'array', // <-- fix,
        'type_id' => 'integer', // Assuming type_id is an integer
        'user_id' => 'integer',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getCategoriesAttribute()
{
    return Category::whereIn('id', $this->category_ids ?? [])->get();
}

   public function categories()
    {
        return Category::whereIn('id', $this->category_ids ?? [])->get();
    }
    public function type()
    {
        return $this->belongsTo(OffersType::class, 'type_id');
    }

}
