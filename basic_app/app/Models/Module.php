<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // ✅ import the correct Builder
 use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';

    /** All feature-flag columns */
    public const FEATURES = [
        'company_dashboard_module',
        'company_info_module',
        'company_branch_module',
        'company_category_module',
        'company_type_module',
        'company_size_module',
        'company_offers_type_module',
        'company_offers_module',
        'product_module',
        'employee_module',
        'order_module',
        'order_status_module',
        'region_module',
        'company_delivery_module',
        'payment_module',
        'additional_module'
    ];

    protected $fillable = [
        ...self::FEATURES,
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active'                  => 'boolean',
        'company_dashboard_module'   => 'boolean',
        'company_info_module'        => 'boolean',
        'company_branch_module'      => 'boolean',
        'company_category_module'    => 'boolean',
        'company_type_module'        => 'boolean',
        'company_size_module'        => 'boolean',
        'company_offers_type_module' => 'boolean',
        'company_offers_module'      => 'boolean',
        'product_module'             => 'boolean',
        'employee_module'            => 'boolean',
        'order_module'               => 'boolean',
      'additional_module'            => 'boolean',


    ];

    // ---------- Relations ----------
    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id');
    }

    // ---------- Scopes ----------
    /** Only rows where overall is_active = true */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** At least one feature flag is true */
    public function scopeAnyFeatureActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            foreach (self::FEATURES as $f) {
                $q->orWhere($f, true);
            }
        });
    }

    // ---------- Feature labels & helpers ----------
    public static function featureLabels(): array
    {
        return [
            'company_dashboard_module'   => __('adminlte::adminlte.module_dashboard'),
            'company_info_module'        => __('adminlte::adminlte.module_info'),
            'company_branch_module'      => __('adminlte::adminlte.module_branch'),
            'company_category_module'    => __('adminlte::adminlte.module_category'),
            'company_type_module'        => __('adminlte::adminlte.module_type'),
            'company_size_module'        => __('adminlte::adminlte.module_size'),
            'company_offers_type_module' => __('adminlte::adminlte.module_offers_type'),
            'company_offers_module'      => __('adminlte::adminlte.module_offers'),
            'product_module'             => __('adminlte::adminlte.module_product'),
            'employee_module'            => __('adminlte::adminlte.module_employee'),
            'order_module'               => __('adminlte::adminlte.module_order'),
        ];
    }

    /** Keys of features that are true on this row */
    public function activeFeatureKeys(): array
    {
        return collect(self::FEATURES)
            ->filter(fn ($f) => (bool) $this->{$f})
            ->values()
            ->all();
    }

    /** Labels of features that are true (one by one) */
    public function activeFeatureLabels(): array
    {
        $labels = self::featureLabels();

        return collect(self::FEATURES)
            ->filter(fn ($f) => (bool) $this->{$f})
            ->map(fn ($f) => $labels[$f] ?? $f)
            ->values()
            ->all();
    }

    /** Joined preview text if you need it */
    public function activeFeaturesText(string $sep = ' • '): string
    {
        return implode($sep, $this->activeFeatureLabels())
            ?: __('adminlte::adminlte.no_features');
    }
}
