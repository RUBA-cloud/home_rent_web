<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFeature extends Model
{
    protected $table = 'home_features';

    protected $fillable = [
        'name_en','name_ar',
        'description_en','description_ar',
        'image','user_id','is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ Many-to-Many Homes
    public function homeRents()
    {
        return $this->belongsToMany(
            HomeRent::class,
            'home_rent_home_feature',
            'home_feature_id',
            'home_rent_id'
        )->withTimestamps();
    }
}
