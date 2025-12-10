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

    // المنتجات التابعة للفئة
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id')
            ->with([
                'images',
                'sizes',
                'additionals',
                'category',
                'type',
            ]);
    }

    /**
     * سكوب لتصفية الكاتيجوري مع منتجاتها
     * الاستخدام:
     * Category::searchCategory($categoryId, $typeId, $color, $sizeId, $minPrice, $maxPrice)->get();
     */
    public function scopeSearchCategory(
        Builder $query,
        ?int $categoryId = null,
        ?int $typeId = null,
        ?string $color = null,
        ?int $sizeId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): Builder {
        return $query
            // فلتر على الكاتيجوري نفسها (id)
            ->when($categoryId, function (Builder $q) use ($categoryId) {
                $q->where('id', $categoryId);
            })
            // نحمل المنتجات مع فلاتر إضافية
            ->with(['products' => function ($q) use ($typeId, $color, $sizeId, $minPrice, $maxPrice) {
                $q->where('is_active', true)
                    ->when($typeId, function ($qq) use ($typeId) {
                        $qq->where('type_id', $typeId);
                    })
                    ->when($sizeId, function ($qq) use ($sizeId) {
                        $qq->where('size_id', $sizeId);
                    })
                    ->when($color, function ($qq) use ($color) {
                        $normalized = trim(strtolower($color));

                        $qq->where(function ($inner) use ($normalized) {
                            // لو عندك عمود عادي اسمه colors
                            $inner->where('colors', $normalized);

                            // ولو عندك عمود JSON اسمه colors (array of strings)
                            $inner->orWhereJsonContains('colors', $normalized);
                        });
                    });

                // فلترة السعر (مين ومكس) على مستوى المنتجات
                if ($minPrice !== null) {
                    $q->where('price', '>=', $minPrice);
                }

                if ($maxPrice !== null) {
                    $q->where('price', '<=', $maxPrice);
                }

                $q->with([
                    'images',
                    'sizes',
                    'additionals',
                    'category',
                    'type',
                ]);
            }]);
    }
}
