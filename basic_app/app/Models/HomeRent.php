<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeRent extends Model
{
    protected $table = 'home_rents';

    protected $fillable = [
        'name_en', 'name_ar', 'category_id',
        'total_ratings', 'longitude', 'latitude',
        'number_of_bedrooms', 'number_of_bathrooms',
        'rent_price', 'description_en', 'description_ar',
        'is_available', 'image', 'video', 'user_id',
        'average_rating','payment_status','address_en',
        'address_ar','size','is_feature'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // ✅ Many-to-Many Features
    public function homeFeatures()
    {
        return $this->belongsToMany(
            HomeFeature::class,
            'home_rent_home_feature',
            'home_rent_id',
            'home_feature_id'
        )->withTimestamps();
    }

    public function faviorates(){

    }
}
