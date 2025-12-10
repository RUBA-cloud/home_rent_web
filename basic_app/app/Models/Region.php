<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    //
    protected $table = 'regions'; // Ensure the table name is correct
    protected $fillable = [
        'country_en',
        'country_ar',
        'city_en',
        'city_ar',
        'excepted_day_count',
        'is_active',
        'user_id',
         // Assuming you have an offer percentage field
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'integer',
        'category_id' => 'integer',
    ];
     public function user() {return $this->belongsTo(User::class, 'user_id');}
}
