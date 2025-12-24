<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HomeRentRequest;
use App\Models\HomeRent;
use App\Models\HomeRentHistory;
use App\Models\Category;
use App\Models\HomeFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\HomeRentEvent;

class HomeRentController extends Controller
{
    public function index(Request $request)
    {
        $isHistory = $request->boolean('isHistory');

        $homeRents = $isHistory
            ? HomeRentHistory::with('user')->paginate(5)
            : HomeRent::with('user')->paginate(5);

        return view('homeRent.index', compact('homeRents', 'isHistory'));
    }

    public function create()
    {
        $categories   = Category::where('is_active', true)->get();
        $homeFeatures = HomeFeature::where('is_active', true)->get();

        return view('homeRent.create', [
            'categories'   => $categories,
            'homeFeatures' => $homeFeatures,
            'homeRent'     => new HomeRent(),
        ]);
    }

    public function store(HomeRentRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {

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

            $data['user_id'] = $data['user_id'] ?? Auth::id();

            /** @var HomeRent $homeRent */
            $homeRent = HomeRent::create($data);

            // ✅ Sync pivot features
            $featureIds = $request->input('home_rent_features', []);
            if (is_array($featureIds)) {
                $homeRent->homeFeatures()->sync($featureIds);
            }

            // ✅ Broadcast create event (optional)
            try {
                broadcast(new HomeRentEvent($homeRent->fresh(['user','category','homeFeatures'])));
            } catch (\Throwable $e) {
                Log::warning('HomeRentCreated broadcast failed: ' . $e->getMessage());
            }
        });

        return redirect()
            ->route('homeRent.index')
            ->with('success', 'Home rent created successfully.');
    }

    public function show(string $id)
    {
        $isHistory = false;

        $homeRent = HomeRent::with(['user','category','homeFeatures'])->find($id);

        if (!$homeRent) {
            $homeRent = HomeRentHistory::where('home_rent_id', $id)
                ->latest('created_at')
                ->firstOrFail();
            $isHistory = true;
        }


        return view('homeRent.show', compact('homeRent', 'isHistory'));
    }

    public function edit(string $id)
    {
        $homeRent     = HomeRent::with('homeFeatures')->findOrFail($id);
        $categories   = Category::where('is_active', true)->get();
        $homeFeatures = HomeFeature::where('is_active', true)->get();

        return view('homeRent.edit', [
            'homeRent'     => $homeRent,
            'categories'   => $categories,
            'homeFeatures' => $homeFeatures,
        ]);
    }

    public function update(HomeRentRequest $request, string $id)
    {
        $homeRent = HomeRent::findOrFail($id);
        $data = $request->validated();

        DB::transaction(function () use ($request, $homeRent, $data) {

            // ✅ Save history snapshot BEFORE update
            $historyData                 = $homeRent->toArray();
            $historyData['home_rent_id'] = $homeRent->id;
            $historyData['action']       = 'update';
            $historyData['performed_by'] = Auth::id();
            HomeRentHistory::create($historyData);

            // ✅ Handle files (full URL)
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('home_rent_images', 'public');
                $data['image'] = asset('storage/' . $path);
                        }

            if ($request->hasFile('video')) {
                $path = $request->file('video')->store('home_rent_videos', 'public');
                $data['video'] = asset('storage/' . $path);
            }

            // ✅ Update main record
            $homeRent->update($data);

            // ✅ Sync pivot features (FIXED)
            $featureIds = $request->input('home_rent_features', []);
            if (is_array($featureIds)) {
                $homeRent->homeFeatures()->sync($featureIds);
            }

            // ✅ Broadcast update event (optional)
            try {
                broadcast(new HomeRentEvent($homeRent->fresh(['user','category','homeFeatures'])));
            } catch (\Throwable $e) {
                Log::warning('HomeRentUpdated broadcast failed: ' . $e->getMessage());
            }
        });

        return redirect()
            ->route('homeRent.index')
            ->with('success', 'Home rent updated successfully.');
    }

    public function destroy(string $id)
    {
        $homeRent = HomeRent::findOrFail($id);

        DB::transaction(function () use ($homeRent) {

            // ✅ Save history snapshot BEFORE delete
            $historyData                 = $homeRent->toArray();
            $historyData['home_rent_id'] = $homeRent->id;
            $historyData['action']       = 'delete';
            $historyData['performed_by'] = Auth::id();
            $historyData['is_active']    = false;

            HomeRentHistory::create($historyData);

            // ✅ Detach pivot features then delete
            $homeRent->homeFeatures()->detach();
            $homeRent->delete();

            // ✅ Broadcast delete event (optional)
            try {
                broadcast(new HomeRentEvent($homeRent));
            } catch (\Throwable $e) {
                Log::warning('HomeRentDeleted broadcast failed: ' . $e->getMessage());
            }
        });

        return redirect()
            ->route('homeRent.index')
            ->with('success', 'Home rent deleted and archived successfully.');
    }
}
