<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use App\Models\SizeHistory;
use App\Http\Requests\SizeRequest;

class SizeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index($history = false)
    {
        if ($history) {
            $sizes = SizeHistory::with('user')->where('is_active',true)->orderByDesc('created_at')->paginate(5);
            return view('size.history', compact('sizes'));
        }

        $sizes = Size::with('user')
            ->where('is_active', 1)
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('size.index', compact('sizes', 'history'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('size.create');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $size = Size::with('user')->find($id);

        if (!$size) {
            $size = SizeHistory::with('user')->find($id);

            if (!$size) {
                return redirect()->route('sizes.index')->with('error', 'Size not found.');
            }
        }

        return view('size.show', compact('size'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SizeRequest $request)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $logoPath = $request->file('image')->store('size_logo', 'public');
            $validated['image'] = asset('storage/' . $logoPath);
        }

        // Create size
        $size = Size::create($validated);
        $size->user_id = auth()->id();
        $size->save();

        return redirect()->route('sizes.index')->with('success', 'Size created successfully.');
    }

    /**
     * Search active sizes
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $history = false;

        $sizes = Size::with('user')
            ->where(function ($q) use ($searchTerm) {
                $q->where('name_en', 'like', '%' . $searchTerm . '%')
                    ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
                    ->orWhere('descripation', 'like', '%' . $searchTerm . '%')
                    ->orWhere('price', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('Size.index', compact('sizes', 'history'));
    }

    /**
     * Search size history
     */
    public function searchHistory(Request $request)
    {
        $searchTerm = $request->input('search');
        $isHistory = true;

        $sizes = SizeHistory::with('user')
            ->where(function ($q) use ($searchTerm) {
                $q->where('name_en', 'like', '%' . $searchTerm . '%')
                    ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
                    ->orWhere('descripation', 'like', '%' . $searchTerm . '%')
                    ->orWhere('price', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('Size.history', compact('sizes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $size = Size::findOrFail($id);
        return view('Size.edit', compact('size'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SizeRequest $request, string $id)
    {
        $size = Size::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $logoPath = $request->file('image')->store('size_logo', 'public');
            $validated['image'] = asset('storage/' . $logoPath);
        }

        // Store current data in history before update
        $historyData = $size->toArray();
        unset($historyData['id']);
        $historyData['user_id'] = auth()->id();
        SizeHistory::create($historyData);

        // Update current size
        $size->user_id = auth()->id();
        $size->save();
        $size->update($validated);
        broadcast(new \App\Events\SizeEventUpdate($size))->toOthers();
 return redirect()->route('sizes.index')->with('success', 'Size updated successfully.');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $size = Size::findOrFail($id);

        // Store current data in history before soft delete
        $historyData = $size->toArray();
        $historyData['is_active'] = false;
        unset($historyData['id']);
        $historyData['user_id'] = auth()->id();
        SizeHistory::create($historyData);

        // Soft delete by marking as inactive
        $size->update(['is_active' => false]);

        return redirect()->route('sizes.index')->with('success', 'Size deleted successfully.');
    }

    /**
     * Reactivate a soft-deleted size.
     */
    public function reactive($id)
    {
        $size = SizeHistory::findOrFail($id);

        $newSize = $size->toArray();
        unset($newSize['id']); // Remove id to avoid conflict
        $newSize['is_active'] = true;
        $newSize['user_id'] = auth()->id();

        $siz = Size::create($newSize);
        $siz->user_id = auth()->id();
        $siz->save();

        return redirect()->route('sizes.index')->with('success', 'Size reactivated successfully.');
    }
}
