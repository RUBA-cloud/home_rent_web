<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    //
    protected $table = 'type'; // Ensure the table name is correct
    protected $fillable = [
        'name_en',
        'name_ar',
        'is_active',
        'user_id',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function activeTypes()
    {
        return $this->where('is_active', true)->get();
}
}
