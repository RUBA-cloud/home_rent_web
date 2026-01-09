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
    public function index(): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $favorites = FaviorateModel::query()
            ->with([
                'homes',
                'homes.homeFeatures',   // ✅ features
                'homes.category',       // (اختياري)
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Favorites list retrieved successfully.',
            'data' => $favorites,
        ], 200);
    }

    public function search(Request $request): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $search = trim((string) $request->input('search', ''));

        $q = FaviorateModel::query()
            ->with(['home', 'home.homeFeatures', 'home.category'])
            ->where('user_id', $userId);

        // إذا عندك عمود is_active
        if (schema_has_column('faviorate_models', 'is_active')) {
            $q->where('is_active', true);
        }

        if ($search !== '') {
            $q->whereHas('home', function ($qq) use ($search) {
                $qq->where('name_en', 'like', "%{$search}%")
                   ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        $favorites = $q->latest()->get();

        return response()->json([
            'message' => 'Favorites search retrieved successfully.',
            'data' => $favorites,
        ], 200);
    }

    public function clearAllFaviorate(): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $deletedCount = FaviorateModel::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Favorites list removed successfully.',
            'deleted' => $deletedCount,
        ], 200);
    }

    public function addFaviorate(FaviorateRequest $request): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validated();

        $exists = FaviorateModel::where('user_id', $userId)
            ->where('home_id', $data['home_id'])
            ->when(schema_has_column('faviorate_models', 'is_active'), fn($q) => $q->where('is_active', true))
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Home already added to favorites.',
            ], 409);
        }

        $favorite = FaviorateModel::create([
            'user_id' => $userId,
            'home_id' => $data['home_id'],
            ...(schema_has_column('faviorate_models', 'is_active') ? ['is_active' => true] : []),
        ]);

        return response()->json([
            'message' => 'Home added to favorites successfully.',
            'data' => $favorite->load(['homes', 'homes.homeFeatures', 'homes.category']),
        ], 201);
    }

    public function removeFaviorate($id): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $favorite = FaviorateModel::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'Favorite not found.'], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'Favorite removed successfully.',
        ], 200);
    }

    public function removeProductFaviorate($homeId): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
            ], 401);
        }

        $favorite = FaviorateModel::with(['homes', 'homes.homeFeatures', 'homes.category'])
            ->where('user_id', $userId)
            ->where('home_id', $homeId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'status' => false,
                'message' => 'Favorite not found.',
                'data' => null,
            ], 404);
        }

        $deletedFavorite = $favorite->toArray();
        $favorite->delete();

        return response()->json([
            'status' => true,
            'message' => 'Favorite removed successfully.',
            'data' => $deletedFavorite,
        ], 200);
    }
}

/**
 * ✅ helper (بدون ما نكسّر مشروعك)
 * إذا ما بدك helper، احذف where is_active من فوق وخلاص.
 */
if (!function_exists('schema_has_column')) {
    function schema_has_column(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
