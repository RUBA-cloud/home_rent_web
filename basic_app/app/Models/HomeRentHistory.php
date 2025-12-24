<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeRentHistory extends Model
{
    //
    protected $table = 'home_rent_histories';
    protected $fillable = ['name_en', 'category_id', 'name_ar', 'total_ratings', 'longitude', 'latitude', 'number_of_bedrooms', 'number_of_bathrooms', 'rent_price', 'description_en', 'description_ar', 'is_available',
     'image', 'video', 'user_id', 'features', 'average_rating','payemnt_status','address_en','size',
        'address_ar'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }



}
