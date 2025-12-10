<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditonalHistory extends Model
{
    //
    protected $table = 'additonal_history'; // Ensure the table name is correct
    protected $fillable = [
        'name_en',
        'name_ar',
        'image',
        'price',
        'description',
        'is_active',
        'user_id',
        'descripation',

    ];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');}
    public function activeAdditonalHistory()
    {
        return $this->where('is_active', true)->get();  }
}
