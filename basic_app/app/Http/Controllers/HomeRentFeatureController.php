<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HomeFeatureRentRequest;
use App\Models\HomeRent;
use App\Models\HomeFeature;
use App\Models\HomeFeatureHistory;

class HomeRentFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // You named variable $homeRents but it's actually HomeFeature
        $homeRents = HomeFeature::with('user')->paginate(5);

        return view('homeRentFeature.index', compact('homeRents'));
    }
    public function history($isHistory = true)
    {
        $histories = HomeFeatureHistory::with('user')->orderBy('created_at', 'desc')->paginate(5);
        return view('homeRentFeature.history', compact('histories'));
    }    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('homeRentFeature.create');
    }


    /**
     * Store a newly created resource in storage.
     */

    public function reactivate($id){
        $homeFeature = HomeFeatureHistory::withTrashed()->findOrFail($id);
        $homeFeature->restore();

        return redirect()
            ->route('homeRentFeature.index')
            ->with('success', 'Home feature reactivated successfully.');
    }
    public function store(HomeFeatureRentRequest $request)
    {
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('home_rent_images', 'public');

            $request->merge(['image_path' => $imagePath]);
        }

        $request['user_id'] = auth()->id();

        // Create feature
        $homeFeature = HomeFeature::create($request->all());

        // 🔹 Save history (created)
        HomeFeatureHistory::create([
            'home_feature_id' => $homeFeature->id,
            'user_id'         => auth()->id(),
            'action'          => 'created',
            'data'            => $homeFeature->toArray(),
        ]);

        return redirect()
            ->route('homeRentFeature.index')
            ->with('success', 'Home feature created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $homeFeature = HomeFeature::find($id);
        if($homeFeature){

        return view('homeRentFeature.show', compact('homeFeature'));
    }

        $homeFeature = HomeFeatureHistory::findOrFail($id);
        return view('homeRentFeature.show', compact('homeFeature'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $homeFeature = HomeFeature::findOrFail($id);

        return view('homeRentFeature.edit', compact('homeFeature'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HomeFeatureRentRequest $request, string $id)
    {
        $homeFeature = HomeFeature::findOrFail($id);

        // 🔹 Save old data BEFORE update
        $oldData = $homeFeature->toArray();

        // Handle image if re-updated
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('home_rent_images', 'public');
            $request->merge(['image_path' => $imagePath]);
        }
$oldData = $homeFeature->toArray();
$oldData['user_id'] = auth()->id();
        $histories = HomeFeatureHistory::create($oldData);
        $homeFeature->update($request->all());
        return redirect()
            ->route('homeRentFeatures.index')
            ->with('success', 'Home feature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $homeFeature = HomeFeature::findOrFail($id);

        // 🔹 Save old data BEFORE delete
        $oldData= $homeFeature->toArray();
        $oldData['user_id'] = auth()->id();
        $histories = HomeFeatureHistory::create($oldData);
        $histories->user_id = auth()->id();
        // Delete

        $homeFeature->delete();

        return redirect()
            ->route('homeRentFeatures.index')
            ->with('success', 'Home feature deleted successfully.');
    }
}
