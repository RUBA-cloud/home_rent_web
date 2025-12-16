<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HomeRentRequest;
use App\Models\HomeRent;
use App\Models\HomeRentHistory;
use App\Models\Category;
use App\Models\HomeFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\HomeRentEvent;

class HomeRentController extends Controller
{
    // /homeRent?isHistory=1
    public function index(Request $request)
    {
        $isHistory = $request->boolean('isHistory');

        if ($isHistory) {
            $homeRents = HomeRentHistory::with('user')->paginate(5);
        } else {
            $homeRents = HomeRent::with('user')->paginate(5);
        }

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



    public function show(string $id)
    {
        $isHistory = false;

        $homeRent = HomeRent::find($id);

        if (! $homeRent) {
            $homeRent = HomeRentHistory::where('home_rent_id', $id)
                ->latest('created_at')
                ->firstOrFail();
            $isHistory = true;
        }

        return view('homeRent.show', compact('homeRent', 'isHistory'));
    }
    public function store(HomeRentRequest $request)
{
    $data = $request->validated();

    DB::transaction(function () use ($request, &$data, &$homeRent) {

        // ✅ Upload image (store full URL)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('home_rent_images', 'public');
            $data['image'] = $request->getSchemeAndHttpHost() . '/storage/' . $path;
        }

        // ✅ Upload video (store full URL)
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('home_rent_videos', 'public');
            $data['video'] = $request->getSchemeAndHttpHost() . '/storage/' . $path;
        }

        // ✅ Set user
        $data['user_id'] = $data['user_id'] ?? Auth::id();

        // ✅ Create home rent
        $homeRent = HomeRent::create($data);

        // ✅ Sync many-to-many features
        // IMPORTANT: make sure your form input name = home_rent_features[]
        $featureIds = $request->input('home_rent_features', []);
        if (is_array($featureIds)) {
            $homeRent->homeFeatures()->sync($featureIds);
        }
    });

    return redirect()
        ->route('homeRent.index')
        ->with('success', 'Home rent created successfully.');
}


    public function edit(string $id)
    {
        $homeRent     = HomeRent::findOrFail($id);
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

        DB::transaction(function () use ($request, $homeRent) {
            $data = $request->validated();

            // ---- Save history snapshot ----
            $historyData                 = $homeRent->toArray();
            $historyData['home_rent_id'] = $homeRent->id;
            $historyData['action']       = 'update';
            $historyData['performed_by'] = Auth::id();

            HomeRentHistory::create($historyData);

            // ---- Handle files ----
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('home_rent_images', 'public');
            }

            if ($request->hasFile('video')) {
                $data['video'] = $request->file('video')->store('home_rent_videos', 'public');
            }

            // ---- Update main record ----
            $homeRent->update($data);

            if ($request->filled('home_rent_features')) {
                $homeRent->features()->sync($request->input('home_rent_features'));
            }

            // ---- Broadcast update event ----
            try {
                $freshHomeRent = $homeRent->fresh();
                broadcast(new HomeRentEvent($freshHomeRent));
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
try {
            broadcast(new HomeRentEvent($homeRent));
            DB::transaction(function () use ($homeRent) {
            $historyData                 = $homeRent->toArray();
            $historyData['home_rent_id'] = $homeRent->id;
            $historyData['action']       = 'delete';
            $historyData['performed_by'] = Auth::id();
            $historyData['is_active']    =false;

            HomeRentHistory::create($historyData);

            $homeRent->delete();
        });

        }
        catch (\Throwable $e) {}

        return redirect()
            ->route('homeRent.index')
            ->with('success', 'Home rent deleted and archived successfully.');
    }
}
