<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'color',
        'quantity',
        'price',
        'total_price',
    ];

    protected $casts = [
        'quantity'    => 'integer',
        'price'       => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /* =========================
       العلاقات
    ========================== */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* =========================
       أحداث النموذج
       نحسب total_price تلقائياً = quantity * price إذا لم يُرسل
    ========================== */
    protected static function booted()
    {
        static::saving(function (OrderItem $item) {
            if (is_null($item->total_price)) {
                $qty   = (int) $item->quantity;
                $price = (string) $item->price;   // يدعم decimal:2
                $item->total_price = bcmul($price, (string) $qty, 2);
            }
        });
    }
}
