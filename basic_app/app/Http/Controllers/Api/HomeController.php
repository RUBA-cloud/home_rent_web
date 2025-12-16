<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || $user->latitude === null || $user->longitude === null) {
            return response()->json([
                'status'  => false,
                'message' => 'User location is missing. Please update latitude/longitude.',
            ], 422);
        }

        $lat = (float) $user->latitude;
        $lng = (float) $user->longitude;

        $radius = 50; // km
        $limit  = 20;

        $distanceSql = "(6371 * acos(
            cos(radians(?)) * cos(radians(home_rents.latitude)) *
            cos(radians(home_rents.longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(home_rents.latitude))
        ))";

        $categories = Category::query()
            ->where('is_active', 1)
            ->with(['homes' => function ($q) use ($distanceSql, $lat, $lng, $radius, $limit) {
                $q->where('is_available', 1)
                  ->whereNotNull('home_rents.latitude')
                  ->whereNotNull('home_rents.longitude')
                  ->with(['user', 'homeFeatures']) // ✅ load both
                  ->select('home_rents.*')
                  ->selectRaw("$distanceSql AS distance_km", [$lat, $lng, $lat])
                  ->having('distance_km', '<=', $radius)
                  ->orderBy('distance_km', 'asc')
                  ->limit($limit);
            }])
            ->get()
            ->filter(fn ($cat) => $cat->homes->isNotEmpty())
            ->values();

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ]);
    }
}
