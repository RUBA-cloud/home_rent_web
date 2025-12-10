<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Paginated list of active categories that have at least one active product.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);

        try {
            $categories = Category::query()
                ->with(['products' => function ($q) {
                    // Only active products
                    $q->where('is_active', true);
                }])
                ->where('is_active', true)
                ->whereHas('products', function ($q) {
                    $q->where('is_active', true);
                })
                ->paginate($perPage);

            return response()->json([
                'status'  => 'ok',
                'message' => 'Categories retrieved.',
                'data'    => $categories,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to retrieve categories.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/categories/{id}
     * Single active category with its active products.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = Category::query()
                ->with(['products' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->where('is_active', true)
                ->whereHas('products', function ($q) {
                    $q->where('is_active', true);
                })
                ->find($id);

            if (! $category) {
                return response()->json([
                    'status'  => 'not_found',
                    'message' => 'Category not found or inactive.',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'status'  => 'ok',
                'message' => 'Category retrieved.',
                'data'    => $category,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to retrieve category.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * SEARCH
     * GET /api/categories/search
     *
     * Available query params:
     * - name / q      : partial match on category name
     * - active        : 1 / 0   (filter by is_active)
     * - has_products  : 1 / 0   (filter only categories that have products)
     * - per_page      : pagination size

 * Search categories by name_en / name_ar with products
 */
public function search(Request $request): JsonResponse
{
    try {
        // Search text from query string: ?q=...
        $search  = $request["search"];
        $perPage = (int) $request->query('per_page', 10);

        // Base query with products (only active products)
        $query = Category::with(['products' => function ($q) {
            $q->where('is_active', true);
        }]);

        // Optional search on category name_en / name_ar
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'LIKE', "%{$search}%")
                  ->orWhere('name_ar', 'LIKE', "%{$search}%");
            });
        }

        $categories = $query->paginate($perPage);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Search results.',
            'data'    => $categories,
        ], 200);

    } catch (Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Search failed.',
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}



}
