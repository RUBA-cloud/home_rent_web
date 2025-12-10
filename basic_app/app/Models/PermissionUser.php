<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionUser extends Model
{
    //
    public function users()
{
    return $this->belongsToMany(User::class)->withTimestamps();
}

public function getDisplayNameAttribute(): string
{
    return app()->getLocale() === 'ar'
        ? ($this->name_ar ?: $this->name_en)
        : ($this->name_en ?: $this->name_ar);
}

}
