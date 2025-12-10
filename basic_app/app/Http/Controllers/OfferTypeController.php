<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferTypeRequest;
use App\Models\OffersType;
use App\Models\OffersTypeHistory;
use Illuminate\Support\Facades\Auth;

class OfferTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($isHistory = false)
    {
        if ($isHistory) {
            $offerTypes = OffersTypeHistory::with('user')->paginate(5);
            return view('OfferType.history', compact('offerTypes'));
        }

        $offerTypes = OffersType::with('user')->paginate(5);

        return view('OfferType.index', compact('offerTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('OfferType.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OfferTypeRequest $request)
    {
        $validated = $request->validated();
        $validated["user_id"]= Auth::user()->id;
        $offerType =OffersType::create($validated);
         $offerType->save();
        return redirect()->route('offers_type.index')
            ->with('success', 'Offer Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $offerType = OffersType::find($id);

        if (!$offerType) {
            $offerType = OffersTypeHsitory::findOrFail($id);
        }

        return view('OfferType.show', compact('offerType'));
    }
    public function reactive($id)
{
    // Find the offer type history record
    $offerTypeHistory = OffersTypeHistory::findOrFail($id);

    // Create new active OffersType based on history
    $newOfferType = new OffersType($offerTypeHistory->toArray());
    $newOfferType->is_active = true;
    $newOfferType->user_id = auth()->id();
    $newOfferType->save();

    return redirect()->route('offers_type.index')
        ->with('success', 'Offer Type has been reactivated successfully.');
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $offerType = OffersType::findOrFail($id);
        return view('OfferType.edit', compact('offerType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OfferTypeRequest $request, string $id)
    {
        $offerType = OffersType::findOrFail($id);

        $validated = $request->validated();
        $offerType->update($validated);
        broadcast(new \App\Events\OfferTypeEventUpdate($offerType))->toOthers();
        return redirect()->route('offers_type.index')
            ->with('success', 'Offer Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
      public function search(Request $request)
{
    $searchTerm = $request->input('search');$isHistory=false;
    $offerTypes = OffersType::with(['user']) // eager load user + branch
            ->where('name_en', 'like', '%' . $searchTerm . '%')
              ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
             ->orWhere('description_en', 'like', '%' . $searchTerm . '%')->orWhere('description_en', 'like', '%' . $searchTerm . '%')

        ->orderBy('created_at', 'desc')
        ->paginate(5);

        return view('OfferType.index', compact('offerTypes'));

}
      public function searchHistory(Request $request)
{
    $searchTerm = $request->input('search');$isHistory=false;
    $offerTypes = OffersTypeHistory::with(['user']) // eager load user + branch
            ->where('name_en', 'like', '%' . $searchTerm . '%')
              ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
             ->orWhere('description_en', 'like', '%' . $searchTerm . '%')->orWhere('description_en', 'like', '%' . $searchTerm . '%')

        ->orderBy('created_at', 'desc')
        ->paginate(5);

          return view('OfferType.history',compact('offerTypes'));

}
    public function destroy(string $id)
    {
       $offerType = OffersType::findOrFail($id);


        // Save current state to history before update
        OffersTypeHistory::create([
            'offer_type_id' => $offerType->id,
            'name_en'       => $offerType->name_en,
            'name_ar'       => $offerType->name_ar,
            'description_en'=> $offerType->description_en,
            'description_ar'=> $offerType->description_ar,
            'is_discount'   => $offerType->is_discount,
            'is_total_gift' => $offerType->is_total_gift,
            'is_total_discount'=>$offerType->is_total_discount,
            'is_product_count_gift' => $offerType->is_product_count_gift,
            'discount_value_product' => $offerType->discount_value_product,
            'discount_value_delivery' => $offerType->discount_value_delivery,
            'products_count_to_get_gift_offer' => $offerType->products_count_to_get_gift_offer,
            'total_gift'    => $offerType->total_gift,
            'total_amount'=>$offerType->total_amount,
            'is_active'     => false,
            'user_id'       => Auth::id(),
        ]);

        $offerType->delete();

        return redirect()->route('offers_type.index')
            ->with('success', 'Offer Type updated successfully.');
    }
}
