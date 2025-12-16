<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFeature extends Model
{
    //
    protected $table = 'home_features';
    protected $fillable = ['name_en', 'name_ar', 'description_en','description_ar',
    'image', 'user_id', 'is_active', 'image',];

    public function user()
    {
        return $this->belongsTo(User::class);
    }



public function homeFeatures()
{
    return $this->belongsToMany(
        HomeFeature::class,
        'home_rent_home_feature',
        'home_rent_id',
        'home_feature_id'
    )->withTimestamps();
}


}
