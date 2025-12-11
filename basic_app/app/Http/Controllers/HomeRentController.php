<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HomeRentRequest;
use App\Models\HomeRent;
use App\Models\HomeFeature;
class HomeRentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($isHistory = false)
    {
        $homeRents = HomeRent::with('user')->paginate(5);
        return view('homeRent.index', compact('homeRents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $homeFeatures = HomeFeature::where('is_active', true)->get();
        return view('homeRent.create', compact('homeFeatures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HomeRentRequest $request)
    {
        //
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('home_rent_images', 'public');
            $request->merge(['image_path' => $imagePath]);
        }

        $homeRent = HomeRent::create($request->all());
        return redirect()->route('homeRent.index')->with('success', 'Home rent created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
