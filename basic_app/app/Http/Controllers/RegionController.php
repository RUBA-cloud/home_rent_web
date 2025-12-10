<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegionRequest;
use App\Models\Region;
use App\Models\RegionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = Region::with('user')

            ->paginate(5);

        return view('region.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('region.create');
    }
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $activeFilter = $request->has('active') ? filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN) : null;

        $regions = Region::query()
            ->when($q !== '', function ($qbuilder) use ($q) {
                $qbuilder->where(function ($w) use ($q) {
                    $w->where('country_en', 'like', "%{$q}%")
                      ->orWhere('country_ar', 'like', "%{$q}%")
                      ->orWhere('city_en', 'like', "%{$q}%")
                      ->orWhere('city_ar', 'like', "%{$q}%");
                });
            })
            ->when(!is_null($activeFilter), function ($qbuilder) use ($activeFilter) {
                $qbuilder->where('is_active', $activeFilter);
            })
            ->with('user')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query()); // keep query string in pagination links

        return view('region.index', compact('regions', 'q', 'activeFilter'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(RegionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $data['user_id'] ?? Auth::id(); // default to current user if not provided
        $data['is_active'] = $data['is_active'] ?? true;

        DB::transaction(function () use ($data) {
            $region = Region::create($data);


        });

        return redirect()
            ->route('regions.index')
            ->with('success', __('Region created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Region $region)
    {
        return view('region.show', compact('region'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Region $region)
    {
        return view('region.edit', compact('region'));
    }

    /**
     * Update the specified resource in storage.
     *
     * Requirement: when update, save the OLD data in region history first.
     */
    public function update(RegionRequest $request, Region $region)
    {
        $data = $request->validated();

        DB::transaction(function () use ($region, $data) {
            // 1) Save OLD snapshot before changing anything
            $this->writeHistory($region, 'updated_before', $region->toArray());

            // 2) Apply updates
            $region->update($data);
broadcast(new \App\Events\RegionEventUpdate($region));
            // 3) (optional) Save NEW snapshot as well
            $this->writeHistory($region, 'updated_after', $region->fresh()->toArray());
        });

        return redirect()
            ->route('regions.index')
            ->with('success', __('Region updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * Requirement: when destroy, save in region history and set is_active = false.
     */
    public function destroy(Region $region)
    {
        DB::transaction(function () use ($region) {
            // Save current state to history with "deleted" action
            $this->writeHistory($region, 'deleted', $region->toArray());

            // Soft deactivate instead of hard delete
            $region->update(['is_active' => false]);
        });

        return redirect()
            ->route('regions.index')
            ->with('success', __('Region deactivated and archived to history.'));
    }

    /**
     * Helper: write a row to RegionHistory from a Region snapshot.
     * Adjust fields to match your RegionHistory table.
     */
    protected function writeHistory(Region $region, string $action, array $snapshot = []): void
    {
        // Map only the fields your RegionHistory table actually has
        $payload = [
            'region_id'          => $region->id,
            'action'             => $action, // e.g., created, updated_before, updated_after, deleted
            'country_en'         => $snapshot['country_en'] ?? $region->country_en,
            'country_ar'         => $snapshot['country_ar'] ?? $region->country_ar,
            'city_en'            => $snapshot['city_en'] ?? $region->city_en,
            'city_ar'            => $snapshot['city_ar'] ?? $region->city_ar,
            'excepted_day_count' => $snapshot['excepted_day_count'] ?? $region->excepted_day_count,
            'is_active'          => $snapshot['is_active'] ?? $region->is_active,
            'user_id'            => $snapshot['user_id'] ?? $region->user_id,
        ];

        RegionHistory::create($payload);
    }
    public function history()
    {
        $history = RegionHistory::with('user')
            ->orderByDesc('id')
            ->paginate(20);

        return view('region.history', compact('history'));
    }
      public function restoreFromHistory(RegionHistory $history)
    {
        DB::transaction(function () use ($history) {
            // Try to find the original region
            $region = Region::find($history->region_id);

            // Only copy the fields that actually exist on Region


            $payload = array_intersect_key($history->toArray(), array_flip($region ?->getFillable() ?? []));
            $payload['is_active'] = true; // ensure it becomes active

            if ($region) {
                // Snapshot CURRENT region before overwriting
                RegionHistory::create([
                    'region_id'          => $region->id,
                    'country_en'         => $region->country_en,
                    'country_ar'         => $region->country_ar,
                    'city_en'            => $region->city_en,
                    'city_ar'            => $region->city_ar,
                    'excepted_day_count' => $region->excepted_day_count,
                    'is_active'          => $region->is_active,
                    'user_id'            => $region->user_id,
                ]);

                // Apply the restoration
                $region->update($payload);

                // Optional: snapshot AFTER restore
                RegionHistory::create([
                    'region_id'          => $region->id,
                    'country_en'         => $region->country_en,
                    'country_ar'         => $region->country_ar,
                    'city_en'            => $region->city_en,
                    'city_ar'            => $region->city_ar,
                    'excepted_day_count' => $region->excepted_day_count,
                    'is_active'          => true,
                    'user_id'            => $region->user_id,
                ]);
            } else {
                // If region row no longer exists, recreate it from history
                $region = Region::create($payload);

                RegionHistory::create([
                    'region_id'          => $region->id,
                    'country_en'         => $region->country_en,
                    'country_ar'         => $region->country_ar,
                    'city_en'            => $region->city_en,
                    'city_ar'            => $region->city_ar,
                    'excepted_day_count' => $region->excepted_day_count,
                    'is_active'          =>  true,
                    'user_id'            => $region->user_id,
                ]);
            }
        });

        return redirect()
            ->route('regions.index')
            ->with('success', __('Region restored and activated from history.'));
    }

}
