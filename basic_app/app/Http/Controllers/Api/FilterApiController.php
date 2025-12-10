<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest; // fixed typo
use App\Models\Category;
use App\Models\Type;
use App\Models\Size;
use App\Models\Product;
use Illuminate\Http\Request;

class FilterApiController extends Controller
{
    /**
     * GET /api/filters
     * Returns all filter data (categories, types, sizes) and
     * derived filters (min/max price, colors) from the first category that has products.
     */
    public function index()
    {
        // Basic lists
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $types = Type::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $sizes = Size::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        // First active category that HAS active products
        $firstCategory = Category::query()
            ->where('is_active', true)
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->with(['products' => function ($q) {
                $q->where('is_active', true)
                  ->select('id', 'category_id', 'price',  'colors'); // color (string) + colors (json)
            }])
            ->orderBy('id')
            ->first();

        $minPrice = null;
        $maxPrice = null;
        $colors   = [];
        $categoryId = $firstCategory?->id;

        if ($firstCategory) {
            $products = collect($firstCategory->products);

            // Prices
            $prices = $products->pluck('price')
                ->filter(fn ($p) => $p !== null && is_numeric($p))
                ->map(fn ($p) => (float) $p);

            if ($prices->isNotEmpty()) {
                $minPrice = $prices->min();
                $maxPrice = $prices->max();
            }

            // Colors from 'color' (single string)
            $singleColors = $products->pluck('color')
                ->filter()
                ->map(fn ($c) => is_string($c) ? trim(strtolower($c)) : $c);

            // Colors from 'colors' (json/array)
            $jsonColors = $products->pluck('colors')
                ->flatMap(function ($val) {
                    if (is_array($val)) {
                        return $val;
                    }
                    if (is_string($val)) {
                        $decoded = json_decode($val, true);
                        return is_array($decoded) ? $decoded : [];
                    }
                    return [];
                })
                ->filter()
                ->map(fn ($c) => is_string($c) ? trim(strtolower($c)) : $c);

            $colors = $singleColors->merge($jsonColors)
                ->unique()
                ->values()
                ->toArray();
        }

        // Order: status, data, products (no products here by spec)
        return response()->json([
            'status' => true,
            'data'   => [
                'categories'  => $categories,
                'types'       => $types,
                'sizes'       => $sizes,
                'category_id' => $categoryId,
                'min_price'   => $minPrice,
                'max_price'   => $maxPrice,
                'colors'      => $colors,
            ],
        ]);
    }
public function filter(FilterRequest $request)
{
    $categories = Category::searchCategory(
        $request->input('category_id'),
        $request->input('type_id'),
        $request->input('color'),
        $request->input('size_id'),
        $request->input('price_form'),
        $request->input('price_to')

    )->get(); // IMPORTANT: execute query

    return response()->json([
        'status' => true,
        'data'   => [
            'categories' => $categories,
        ],
    ]);
}

}
