<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderHistory extends Model
{
    use HasFactory;

    protected $table = 'orders_table_history';

    protected $fillable = [
        'user_id',
        'employee_id',
        'offer_id',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    // اختياري: ثوابت لحالات الطلب
    public const STATUS_PENDING    = 0;
    public const STATUS_PROCESSING = 1;
    public const STATUS_COMPLETED  = 2;
    public const STATUS_CANCELED   = 3;

    /* =========================
       العلاقات
    ========================== */

    // العميل
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
    // الموظف المسؤول
    // إن كان لديك جدول employees استبدل User::class بـ Employee::class
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // منتجات الطلب (للاطلاع السريع)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot(['id','color','quantity','price','total_price'])
                    ->withTimestamps();
    }

    /* =========================
       خصائص محسوبة (غير مخزنة)
    ========================== */

    // إجمالي الكمية
    protected function totalQuantity(): Attribute
    {
        return Attribute::get(fn () => (int) $this->items()->sum('quantity'));
    }

    // إجمالي السعر
    protected function totalAmount(): Attribute
    {
        return Attribute::get(fn () => (string) $this->items()->sum('total_price'));
    }

    /* =========================
       سكوبات مساعدة
    ========================== */
    public function scopeStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
