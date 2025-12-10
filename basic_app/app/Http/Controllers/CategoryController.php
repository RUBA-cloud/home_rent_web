<?php

namespace App\Http\Controllers;

use App\Events\CategoryUpdateEvent;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\CompanyBranch;
use App\Models\CategoryHistory;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index($isHistory = false)
    {
        if ($isHistory) {
            $categories = CategoryHistory::with('user')->paginate(5);
            return view('Category.history', compact('categories'));
        }

        $categories = Category::with('user')->where('is_active', true)->paginate(5);
        return view('Category.index', compact('categories', 'isHistory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = CompanyBranch::where('is_active', true)->get();
        return view('Category.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {

        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $logoPath = $request->file('image')->store('category_logo', 'public');
            $validated['image'] = asset('storage/' . $logoPath);
        }

        // Create category
        $category = Category::create($validated);
        $category->user_id = auth()->id();
        $category->save();

        // Attach branches if provided
        if ($request->filled('branch_ids')) {
            $category->branches()->attach($request->branch_ids);
        }

        return redirect()->route('categories.index')->with('success', '');
    }

        public function searchHistory(Request $request){

        $searchTerm = $request->input('search');
        $categories = CategoryHistory::with(['user', 'branches']) // eager load user + branch
        ->whereHas('branches', function ($q) use ($searchTerm) {
            $q->where('name_en', 'like', '%' . $searchTerm . '%')
              ->orWhere('name_ar', 'like', '%' . $searchTerm . '%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(5);

            return view('Category.history', compact('categories'));
        }
       public function search(Request $request)
{
    $searchTerm = $request->input('search');$isHistory=false;
    $categories = Category::with(['user', 'branches']) // eager load user + branch
        ->whereHas('branches', function ($q) use ($searchTerm) {
            $q->where('name_en', 'like', '%' . $searchTerm . '%')
              ->orWhere('name_ar', 'like', '%' . $searchTerm . '%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(5);

          return view('Category.index', compact('categories', 'isHistory'));

}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with('branches')->find($id);

        if (!$category) {
            $category = CategoryHistory::with('branches')->findOrFail($id);
        }

        return view('Category.show', compact('category'));
    }

    /**
     * Reactivate a category from history.
     */
    public function reactivate(string $id)
    {
        $historyCategory = CategoryHistory::with('branches')->findOrFail($id);
        $historyCategory->user_id = auth()->id(); // Set the current user as the reactivating user
        $historyCategory->created_at = now(); // Reset the creation date to now
        $historyCategory->updated_at = now(); // Reset the update date to now
        $historyCategory->is_active = true;
        $historyCategory->save();

        // Prepare data for new live category
        $historyData = $historyCategory->toArray();
        unset($historyData['id']);

        // Preserve image if exists
        if (!empty($historyCategory->image)) {
            $historyData['image'] = $historyCategory->image;
        }

        // Create new live category
        $newCategory = Category::create($historyData);

        // Attach branches if any
        if (!empty($historyData['branches'])) {
            $branchIds = collect($newCategory->branches)->pluck('id');

            $newCategory->branches()->attach($branchIds);
        }

        return redirect()->route('categories.index')->with('success', 'Category reactivated successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $branches = CompanyBranch::where('is_active', true)->get();
        $category = Category::with('branches')->findOrFail($id);

        return view('Category.edit', compact('category', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $validated = $request->validated();
        $category = Category::with('branches')->findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('category_logo', 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        // Store old data in history
        $historyData = $category->toArray();
        unset($historyData['id']);
        if (!empty($category->image)) {
            $historyData['image'] = $category->image;
        }

        $categoryHistory = CategoryHistory::create($historyData);

        // Sync branches to history
        if (!empty($historyData['branches'])) {
            $branchIds = collect($category->branches)->pluck('id');
            $categoryHistory->branches()->sync($branchIds);

        }

        // Update live category
        $category->update($validated);
        broadcast(new CategoryUpdateEvent($category))->toOthers();
        // Sync branches if provided
        if ($request->filled('branch_ids')) {
            $category->branches()->sync($request->branch_ids);
        } else {
            $category->branches()->detach();
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Prepare history data
        $historyData = $category->toArray();
        unset($historyData['id'], $historyData['created_at'], $historyData['updated_at']);
        $historyData['user_id'] = auth()->id();

        // Store in history
        $categoryHistory = CategoryHistory::create($historyData);

        // Sync branches to history
        if (!empty($historyData['branches'])) {
            $branchIds = collect($category->branches)->pluck('id');
            $categoryHistory->branches()->attach($branchIds);
        }

        // Delete live category (soft delete or permanent based on your setup)
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
