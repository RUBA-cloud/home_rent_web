<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaviorateRequest;
use App\Models\FaviorateModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaviorateController extends Controller
{
    /**
     * قائمة المفضلة للمستخدم الحالي
     */
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // FIXED: where() usage
        $favorites = FaviorateModel::with('product')
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'message' => 'Favorites list retrieved successfully.',
            'data'    => $favorites,
        ], 200);
    }

    /**
     * بحث في المفضلة للمستخدم الحالي
     * مثال: /api/favorites/search?search=iphone
     */
    public function search(Request $request): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $search = trim((string) $request->input('search', ''));

        // لو مافي كلمة بحث رجّع نفس نتيجة index
        if ($search === '') {
            $favorites = FaviorateModel::with('product')
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->get();

            return response()->json([
                'message' => 'Favorites list retrieved successfully.',
                'data'    => $favorites,
            ], 200);
        }

        // نفترض أن حقل اسم المنتج هو name_en / name_ar داخل علاقة product
        $favorites = FaviorateModel::with('product')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->whereHas('product', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name_en', 'like', "%{$search}%")
                       ->orWhere('name_ar', 'like', "%{$search}%");
                });
            })
            ->get();

        return response()->json([
            'message' => 'Favorites search retrieved successfully.',
            'data'    => $favorites,
        ], 200);
    }

    /**
     * إضافة منتج إلى المفضلة
     */

    public function clearAllFaviorate(){
         $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // FIXED: where() usage
        $favorites = FaviorateModel::with('product')
            ->where('user_id', $userId)
            ->delete();

        return response()->json([
            'message' => 'Favorites list removed successfully.',
            'data'    => $favorites,
        ], 200);

    }
    public function addFaviorate(FaviorateRequest $request): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Validate request data
        $data = $request->validated();

        // Check if product is already in favorites
        $exists = FaviorateModel::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->where('is_active', true) // لو عندك toggle للمفضلة
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Product already added to favorites.',
            ], 403); // أو 200 حسب ما تفضلين
        }

        // Add new favorite
        $favorite = FaviorateModel::create([
            'user_id'    => $userId,
            'product_id' => $data['product_id'],
            // 'is_active'  => true, // لو عندك عمود is_active و default مش مفعّل
        ]);

        return response()->json([
            'message' => 'Product added to favorites successfully.',
            'data'    => $favorite->load('product'),
        ], 201);
    }

    /**
     * حذف منتج من المفضلة باستخدام ID الخاص بالمفضلة
     */
    public function removeFaviorate($id): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $favorite = FaviorateModel::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'message' => 'Favorite not found.',
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'Favorite removed successfully.',
        ], 200);
    }

    /**
     * حذف منتج من المفضلة باستخدام product_id
     */
    public function removeProductFaviorate($id): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated.',
                'data'    => null,
            ], 401);
        }

        // نجيب الفيفوريت + المنتج لو عندك علاقة product
        $favorite = FaviorateModel::with('product')
            ->where('user_id', $userId)
            ->where('product_id', $id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'status'  => false,
                'message' => 'Favorite not found.',
                'data'    => null,
            ], 404);
        }

        // نأخذ نسخة من الداتا قبل الحذف
        $deletedFavorite = $favorite->toArray();

        // نحذف السجل
        $favorite->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Favorite removed successfully.',
            'data'    => $deletedFavorite,
        ], 200);
    }
}
