<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferRequest;
use App\Models\Offer;
use App\Models\OffersType;
use App\Models\OffersTypeHistory;
use App\Models\Category;
use App\Models\OffersHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // ✅ This is the missing line

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($isHistory = false)
    {
        if ($isHistory) {
            $offers = OffersHistory::with(['user'])->paginate(5);
            return view('Offer.history', compact('offers'));
        }

        $offers = Offer::with('user')->paginate(5);
        return view('Offer.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $offerTypes = OffersType::where('is_active',true)->get();
        return view('Offer.create', compact('categories', 'offerTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OfferRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        Offer::create($validated);

        return redirect()->route('offers.index')->with('success', 'Offer created successfully.');
    }


public function show(string $id)
{
    // Try to get the Offer first
    $offer = Offer::find($id);
    // If not found, get it from OfferHistory
    if (!$offer) {
        $offer = OffersHistory::findOrFail($id);
    }

    return view('Offer.show', compact('offer'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    { try{
        $categories = Category::where('is_active', true)->get();
        $offerTypes = OffersType::where('is_active',true)->get();
        $offer = Offer::findOrFail($id);



        return view('Offer.edit', compact('categories', 'offerTypes', 'offer'));
    }
    catch( e){}
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(OfferRequest $request, string $id)
    {
        $offer = Offer::findOrFail($id);

        $validated = $request->validated();
        $offer->update($validated);
broadcast(new \App\Events\OfferEventUpdate($offer))->toOthers();
        return redirect()->route('offers.index')->with('success', 'Offer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);

        OffersHistory::create([
            'type_id' => $offer->type_id,
            'category_ids' => $offer->category_ids,
            'discount_percentage' => $offer->discount_percentage,
            'start_date' => $offer->start_date,
            'end_date' => $offer->end_date,
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $offer->delete();

        return redirect()->route('offers.index')->with('success', 'Offer deleted and saved to history.');
    }

    /**
     * Search active offers.
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $offers = Offer::with('user')
            ->where('name_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
            ->orWhere('description_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('description_ar', 'like', '%' . $searchTerm . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('Offer.index', compact('offers'));
    }

    /**
     * Search offer history.
     */
    public function searchHistory(Request $request)
    {
        $searchTerm = $request->input('search');

        $offers = OffersHistory::with('user')
            ->where('name_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
            ->orWhere('description_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('description_ar', 'like', '%' . $searchTerm . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('Offer.history', compact('offers'));
    }

    /**
     * Reactivate an offer from history.
     */
    public function reactive($id)
    {
        $offerHistory = OffersHistory::findOrFail($id);

        $newOffer = new Offer($offerHistory->toArray());
        $newOffer->is_active = true;
        $newOffer->user_id = Auth::id();
        $newOffer->save();

        return redirect()->route('offers.index')->with('success', 'Offer reactivated successfully.');
    }
}
