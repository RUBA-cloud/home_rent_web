<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'employee_id',
        'address',
        'building_number',
        'street_name',
        'lat','long',
        'status',
        'offer_id',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Status codes (match your table/UI):
     * 0 => pending, 1 => accepted, 2 => rejected, 3 => completed
     */
    public const STATUS_PENDING   = 0;
    public const STATUS_ACCEPTED  = 1;
    public const STATUS_REJECTED  = 2;
    public const STATUS_COMPLETED = 3;

    /* =========================
       Relationships
    ========================== */

    /** Customer who placed the order */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Optional alias if you prefer calling it "customer" in views */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Employee handling the order */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /** Offer attached to this order */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    /** Line items */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Products via pivot order_items */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['id', 'color', 'quantity', 'price', 'total_price'])
            ->withTimestamps();
    }

    /* =========================
       Accessors / Computed
    ========================== */

    /** Total quantity across items */
    protected function totalQuantity(): Attribute
    {
        return Attribute::get(fn () => (int) $this->items()->sum('quantity'));
    }

    /** Total amount across items */
    protected function totalAmount(): Attribute
    {
        return Attribute::get(fn () => (string) $this->items()->sum('total_price'));
    }

    /* =========================
       Scopes
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
