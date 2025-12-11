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

public function users()
    {
        return $this->hasOne(User::class);
    }



}
