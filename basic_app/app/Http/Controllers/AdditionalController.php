<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Additonal;
use App\Models\AdditonalHistory;
use App\Http\Requests\AdditonalRequest;

class AdditionalController extends Controller
{
    public function index($isHistory = false)
    {
        if ($isHistory) {
            $additionals = AdditonalHistory::with('user')->paginate(5);
            return view('additionals.history', compact('additionals'));
        }

        $additionals = Additonal::with('user')->where('is_active', true)->paginate(5);
        return view('additionals.index', compact('additionals'));
    }

    public function create()
    {
        return view('additionals.create');
    }

    public function store(AdditonalRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('additonal_images', 'public');
            $data['image'] = asset('storage/' . $image);
        }

        $additonal = Additonal::create($data);

        if ($additonal) {
            return redirect()->route('additional.index')->with('success', 'Additonal product created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create additonal product.');
        }
    }

    public function searchHistory(Request $request)
    {
        $searchTerm = $request->input('search');

        $additionals = AdditonalHistory::with(['user'])
            ->where('name_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
            ->orWhere('descripation', 'like', '%' . $searchTerm . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('additionals.history', compact('additionals'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $additionals = Additonal::with(['user'])
            ->where('name_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
            ->orWhere('descripation', 'like', '%' . $searchTerm . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('additionals.index', compact('additionals'));
    }

    public function show(string $id)
    {
        $additional = Additonal::find($id) ?? AdditonalHistory::findOrFail($id);
        return view('additionals.show', compact('additional'));
    }

    public function edit(string $id)
    {
        $additional = Additonal::findOrFail($id);
        return view('additionals.edit', compact('additional'));
    }

    public function update(AdditonalRequest $request, string $id)
    {
        $additonal = Additonal::findOrFail($id);

        $historyData = $additonal->toArray();
        $historyData['user_id'] = auth()->id();
        AdditonalHistory::create($historyData);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('additonal_images', 'public');
            $data['image'] = asset('storage/' . $imagePath);
        }

        $additonal->update($data);
broadcast(new \App\Events\AdditionalEventUpdate($additonal));
        return redirect()->back()->with('success', 'Additonal product updated successfully.');
    }

    public function destroy(string $id)
    {
        $additonal = Additonal::findOrFail($id);

        $historyData = $additonal->toArray();
        $historyData['user_id'] = auth()->id();
        $historyData['is_active'] =false;
        AdditonalHistory::create($historyData);

        $additonal->delete();

        return redirect()->back()->with('success', 'Additonal product deleted successfully.');
    }

    public function reactive($id)
    {
        $additonalHistory = AdditonalHistory::findOrFail($id);

        $data = $additonalHistory->toArray();
        $data['user_id'] = auth()->id();
        $data['is_active'] = true;
        unset($data['id']);

        Additonal::create($data);

        return redirect()->back()->with('success', 'Additonal product reactivated successfully.');
    }
}
