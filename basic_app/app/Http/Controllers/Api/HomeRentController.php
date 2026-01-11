<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\HomeRent;
use App\Models\HomeFeature;

// ✅ missing imports
use App\Http\Requests\HomeRentRequest;
use App\Events\HomeRentEvent;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Throwable;

class HomeRentController extends Controller
{
    public function features(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);

        try {
            $homeFeatures = HomeFeature::query()
                ->where('is_active', true)
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'status'  => 'ok',
                'message' => 'Home features retrieved.',
                'data'    => $homeFeatures->items(),
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to retrieve home features.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
public function categories(Request $request)
{
    $perPage = (int) $request->query('per_page', 10);

    try {
        $paginator = Category::query()
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Categories retrieved.',
            'data'    => $paginator->items(), // ✅ فقط array
        ], 200);

    } catch (Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to retrieve categories.',
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}

    public function store(HomeRentRequest $request)
    {
        $data = $request->validated();

        try {
            /** @var HomeRent $homeRent */
            $homeRent = DB::transaction(function () use ($request, $data) {

                // ✅ Upload image as full URL
                if ($request->hasFile('image')) {
                    $path = $request->file('image')->store('home_rent_images', 'public');
                    $data['image'] = $request->getSchemeAndHttpHost() . '/storage/' . $path;
                }

                // ✅ Upload video as full URL
                if ($request->hasFile('video')) {
                    $path = $request->file('video')->store('home_rent_videos', 'public');
                    $data['video'] = $request->getSchemeAndHttpHost() . '/storage/' . $path;
                }

                // ✅ force current user if not sent
                $data['user_id'] = $data['user_id'] ?? Auth::id();

                $homeRent = HomeRent::create($data);

                // ✅ Sync pivot features
                $featureIds = $request->input('home_rent_features', []);
                if (is_array($featureIds)) {
                    $homeRent->homeFeatures()->sync($featureIds);
                }

                // ✅ Broadcast create event (optional)
                try {
                    broadcast(new HomeRentEvent(
                        $homeRent->fresh(['user', 'category', 'homeFeatures'])
                    ));
                } catch (Throwable $e) {
                    Log::warning('HomeRentCreated broadcast failed: ' . $e->getMessage());
                }

                return $homeRent->fresh(['user', 'category', 'homeFeatures']);
            });

            return response()->json([
                'status'  => 'ok',
                'message' => 'Home rent created successfully.',
                'data'    => $homeRent,
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create home rent.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
