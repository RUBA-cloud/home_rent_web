<?php

namespace App\Helpers;

use App\Models\User;

class MenuBuilder
{
    public static function build(User $user, string $iconColor = '#000000'): array
    {
        // Always start with dashboard (guarded)
        $menu = [
            [
                'text'       => 'dashboard',
                'url'        => '/home',
                'icon'       => 'fas fa-fw fa-tachometer-alt',
                'icon_color' => $iconColor,
                'can'        => $user->canUseModule('company_dashboard_module'),
            ],
        ];

        // Describe all potential items once (feature flag + menu meta)
        $candidates = [
            [
                'feature'    => 'company_info_module',
                'text'       => 'company_info',
                'url'        => '/companyInfo',
                'icon'       => 'fas fa-fw fa-info-circle',
            ],
            [
                'feature'    => 'company_branch_module',
                'text'       => 'branches',
                'url'        => '/companyBranch',
                'icon'       => 'fas fa-fw fa-code-branch',
            ],
            [
                'feature'    => 'company_category_module',
                'text'       => 'category',
                'url'        => '/categories',
                'icon'       => 'fas fa-fw fa-list',
            ],
             [
                'feature'    => 'home_rent_feature_module',
                'text'       => 'home_rent_feature',
                'url'        => '/homeRentFeatures',
                'icon'       => 'fas fa-fw fa-list',
            ],
             [
                'feature'    => 'home_rent_module',
                'text'       => 'home_rent',
                'url'        => '/homeRent',
                'icon'       => 'fas fa-fw fa-list',
            ],
            [
                'feature'    => 'company_type_module',
                'text'       => 'type',
                'url'        => '/type',
                'icon'       => 'fas fa-fw fa-list',
            ],
            [
                'feature'    => 'company_size_module',
                'text'       => 'size',
                'url'        => '/sizes',
                'icon'       => 'fas fa-fw fa-square',
            ],
            [
                'feature'    => 'company_offers_type_module',
                'text'       => 'offers_type',
                'url'        => '/offers_type',
                'icon'       => 'fas fa-fw fa-list',
            ],
            [
                'feature'    => 'company_offers_module',
                'text'       => 'offers',
                'url'        => '/offers',
                'icon'       => 'fas fa-fw fa-heart',
            ],
            [
                'feature'    => 'product_module',
                'text'       => 'products',
                'url'        => '/product',
                'icon'       => 'fas fa-fw fa-box',
            ],
            [
                'feature'    => 'employee_module',
                'text'       => 'employees',
                'url'        => '/employees',
                'icon'       => 'fas fa-fw fa-users',
            ],
            [
                'feature'    => 'order_module',
                'text'       => 'orders',
                'url'        => '/orders',
                'icon'       => 'fas fa-fw fa-shopping-cart',
            ],
            [
                'feature'    => 'region_module',
                'text'       => 'regions',
                'url'        => '/regions',
                'icon'       => 'fas fa-fw fa-map-marked-alt',
            ],
            [
                'feature'    => 'payment_module',
                'text'       => 'payment',
                'url'        => '/payment',
                'icon'       => 'fas fa-fw fa-credit-card',
            ],
            // Additional items
            [
                'feature'    => 'additional_module',
                'text'       => 'additional',
                'url'        => '/additional',
                'icon'       => 'fas fa-fw fa-plus-square',
            ],
            [
                'feature'    => 'company_delivery_module',
                'text'       => 'company_delivery', // fixed label
                'url'        => '/company_delivery',
                'icon'       => 'fas fa-fw fa-truck',
            ],
        ];

        // Append only items the user can use
        foreach ($candidates as $c) {
            if ($user->canUseModule($c['feature'])) {
                $menu[] = [
                    'text'       => $c['text'],
                    'url'        => $c['url'],
                    'icon'       => $c['icon'],
                    'icon_color' => $iconColor,
                ];
          }
        }

        // Optional admin area (shown regardless of feature flags)
        if (($user->role ?? null) === 'admin') {
            $menu[] = [
                'text'       => 'permissions',
                'url'        => '/permissions',
                'icon'       => 'fas fa-fw fa-user-shield',
                'icon_color' => $iconColor,
            ];
            $menu[] = [
                'text'       => 'modules',
                'url'        => '/modules',
                'icon'       => 'fas fa-fw fa-cogs',
                'icon_color' => $iconColor,
            ];
        }

        // Final pass: if any 'can' keys exist, strip false items; then drop 'can' keys
        $menu = array_values(array_filter($menu, function ($i) {
            return !array_key_exists('can', $i) || $i['can'];
        }));
        foreach ($menu as &$i) {
            unset($i['can']);
        }

        return $menu;
    }
}
