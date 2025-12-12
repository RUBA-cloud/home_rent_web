<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\HomeRentFeature;
class HomeRent extends Model
{
    protected function features()
    {
        return $this->hasMany(HomeFeature::class);
    }
    public function homeFeatures()
    {
        return $this->hasMany(HomeFeature::class);
    }

    protected $table = 'home_rents';
    protected $fillable = ['name_en', 'category_id', 'name_ar', 'total_ratings', 'longitude', 'latitude', 'number_of_bedrooms', 'number_of_bathrooms', 'rent_price', 'description_en', 'description_ar', 'is_available', 'image', 'video', 'user_id', 'features', 'average_rating'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
