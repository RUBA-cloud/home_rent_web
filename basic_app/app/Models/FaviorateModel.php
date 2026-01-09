<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaviorateModel extends Model
{
    //
    protected $table = 'faviorates';
    protected $fillable = ['user_id', 'home_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function homes()
    {
        return $this->belongsTo(HomeRent::class,"home_id");
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
