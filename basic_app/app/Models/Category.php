<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name_en',
        'name_ar',
        'is_active',
        'image',
        'user_id',
    ];

    public function branches()
    {
        return $this->belongsToMany(
            CompanyBranch::class,
            'category_branch',
            'category_id',
            'branch_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categoryHistory()
    {
        return $this->hasMany(CategoryHistory::class, 'category_id');
    }

    // ✅ Homes التابعة للفئة
    public function homes()
    {
        return $this->hasMany(HomeRent::class, 'category_id');
    }

    // ✅ Products التابعة للفئة (بدون with داخل العلاقة)
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // ✅ اختياري: Scope لتحميل homes مع العلاقات المطلوبة
    public function scopeWithHomesDetails(Builder $query): Builder
    {
        return $query->with(['homes' => function ($q) {
            $q->with(['images', 'sizes', 'additionals', 'type', 'user']);
        }]);
    }
}
