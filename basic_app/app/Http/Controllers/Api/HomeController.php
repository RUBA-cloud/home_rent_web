<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeLocationRentRequest;
use App\Models\Category;
use App\Models\HomeRent;
use Illuminate\Http\Request;

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

        $radius = (float) request()->get('radius', 50); // km (optional query param)
        $limit  = (int) request()->get('limit', 20);

        $distanceSql = "(6371 * acos(
            cos(radians(?)) * cos(radians(home_rents.latitude)) *
            cos(radians(home_rents.longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(home_rents.latitude))
        ))";

        $categories = Category::query()
            ->where('is_active', 1)
            ->with(['homes' => function ($q) use ($distanceSql, $lat, $lng, $radius, $limit) {
                $q->where('is_available', 1)
                  ->where('is_available', 1)
                  ->whereNotNull('home_rents.latitude')
                  ->whereNotNull('home_rents.longitude')
                  ->with(['user', 'homeFeatures'])
                  ->select('home_rents.*')
                  ->selectRaw("$distanceSql AS distance_km", [$lat, $lng, $lat])
                  ->havingRaw('distance_km <= ?', [$radius])
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

    /**
     * Get nearby homes by latitude/longitude from request
     * POST body: { "latitude": 31.95, "longitude": 35.93 }
     * Optional query: ?radius=50&limit=20&category_id=1
     */
    public function getHome(HomeLocationRentRequest $request)
    {
        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        $radius = (float) $request->query('radius', 50); // km
        $limit  = (int) $request->query('limit', 20);

        $categoryId = $request->query('category_id'); // optional filter

        $distanceSql = "(6371 * acos(
            cos(radians(?)) * cos(radians(home_rents.latitude)) *
            cos(radians(home_rents.longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(home_rents.latitude))
        ))";

        $homes = HomeRent::query()
            ->where('is_available', 1)
            ->where('is_available', 1)
            ->when($categoryId, fn($q) => $q->where('category_id', (int)$categoryId))
            ->whereNotNull('home_rents.latitude')
            ->whereNotNull('home_rents.longitude')
            ->with(['user', 'homeFeatures', 'category'])
            ->select('home_rents.*')
            ->selectRaw("$distanceSql AS distance_km", [$lat, $lng, $lat])
            ->havingRaw('distance_km <= ?', [$radius])
            ->orderBy('distance_km', 'asc')
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $homes,
        ]);
    }
}
