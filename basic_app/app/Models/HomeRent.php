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
        'features', 'average_rating',
    ];

    // If you still store IDs in `features` column as JSON (optional)
    protected $casts = [
        'features' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * ✅ Many-to-many (pivot table)
     * pivot: home_rent_home_feature (home_rent_id, home_feature_id)
     */
    public function homeFeatures()
    {
        return $this->belongsToMany(
            HomeFeature::class,
            'home_rent_home_feature',
            'home_rent_id',
            'home_feature_id'
        )->withTimestamps();
    }

    /**
     * ✅ Accessor (ONLY if you use `features` column to store IDs)
     * call: $home->features_models
     */
    public function getFeaturesModelsAttribute()
    {
        $ids = $this->features ?? [];

        if (is_string($ids)) {
            $ids = array_filter(array_map('intval', explode(',', $ids)));
        }

        if (!is_array($ids) || empty($ids)) {
            return collect();
        }

        return HomeFeature::whereIn('id', $ids)->get();
    }
}
