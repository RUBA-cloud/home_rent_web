<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyBranchRequest;
use App\Models\CompanyBranch;
use App\Models\CompanyBranchesHistory;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;

class CompanyBranchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $branches = CompanyBranch::with('user')->where('is_active',true)->paginate(5);
        return view('company_branch.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){

        return view('company_branch.create');
    }
public function history(){



            $branches = CompanyBranchesHistory::with('user')->paginate(5);
            return view('company_branch.history', compact('branches'));

}

    public function searchHistory(Request $request)
    {
        $searchTerm = $request->input('search');
        $branches = CompanyBranchesHistory::with('user')
            ->where('name_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
            ->orWhere('address_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('address_ar', 'like', '%' . $searchTerm . '%')
            ->orWhere('email', 'like', '%' . $searchTerm . '%')
            ->orWhere('phone', 'like', '%' . $searchTerm . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

            return view('company_branch.history', compact('branches'));
    }


    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $branches = CompanyBranch::with('user')
            ->where('name_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
            ->orWhere('address_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('address_ar', 'like', '%' . $searchTerm . '%')
            ->orWhere('email', 'like', '%' . $searchTerm . '%')
            ->orWhere('phone', 'like', '%' . $searchTerm . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

            return view('company_branch.index', compact('branches'));
    }




    /**
     * Store a newly created resource in storage.
     */
  public function store(CompanyBranchRequest $request)
{
    if ($request->validated()) {
        $validated = $request->validated();

        $company = CompanyInfo::first();
        if ($company) {

            // Image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('branch_images', 'public');
                $validated['image'] = $request->getSchemeAndHttpHost() . '/storage/' . $imagePath;
            }

            // ✅ Fix working_days (array → string)
            if ($request->has('working_days') && is_array($request->working_days)) {
                $validated['working_days'] = implode(',', $request->working_days);
            }

            // ✅ If location is array (e.g. ['lat' => .., 'lng' => ..]) → store as JSON
            if ($request->has('location') && is_array($request->location)) {
                $validated['location'] = json_encode($request->location);
            }

            $branch = CompanyBranch::create($validated);
            $branch->user_id = auth()->id();
            $branch->company_id = $company->id;
            $branch->save();

            return redirect()
                ->route('companyBranch.index')
                ->with('success', 'Company branch created successfully.');
        }

        return redirect()
            ->back()
            ->withErrors(['error' => 'Company information not found.']);
    }

    return redirect()
        ->back()
        ->withErrors($request->errors())
        ->withInput();
}


    /**
     * Display the specified resource.
     */

     public function show(string $id)
{
    // 1) Try the main table
    $branch = CompanyBranch::with('companyInfo')->find($id);

    // 2) If not found there, fall back to the history table (or throw 404)
    if (! $branch) {
        $branch = CompanyBranchesHistory::with('companyInfo')->find($id);
    }

    // 3) Render the view just once
    return view ('company_branch.show', compact('branch'));
}



    /**
     * Show the form for ing the specified resource.
     */
    public function edit(string $id)
    {
        $branch = CompanyBranch::findOrFail($id);
        return view('company_branch.edit', compact('branch'));
    }

    /**
     * Reactivate a deactivated branch.
     */
    public function reactivate(string $id)
    {
        $branchHistory = CompanyBranchesHistory::findOrFail($id);

        // Reactivate: copy data from history back to active table
        $historyData = $branchHistory->toArray();
        unset($historyData['id']);
        $historyData['is_active'] =true;
        // Create back into active branches
        CompanyBranch::create($historyData);

        return redirect()->route('companyBranch.index')->with('success', 'Company branch reactivated successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyBranchRequest $request, string $id)
    {
        if ($request->validated()) {
            $validated = $request->validated();
            $branch = CompanyBranch::findOrFail($id);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('branch_images', 'public');
                $validated['image'] = request()->getSchemeAndHttpHost() . '/storage/' . $imagePath;
            }

            // Store current branch state in history before updating
            $historyData = $branch->toArray();
            unset($historyData['id']);
            $historyData['user_id'] = auth()->id();
            if (!empty($company['image'])) {
                $historyData['image'] = $branch['image'];
            }
            $historyData["company_info_id"] =$branch->company_id;
            if (!empty($data['working_days']) && is_array($data['working_days'])) {
                $validated['working_days'] = implode(',', $data['working_days']);}
            CompanyBranchesHistory::create($historyData);

            $branch->update($validated);
            broadcast(new \App\Events\BranchEventUpdate($branch))->toOthers();
            return redirect()->route('companyBranch.index')->with('success', 'Company branch updated successfully.');
        }

        return redirect()->back()->withErrors($request->errors())->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = CompanyBranch::findOrFail($id);

        // Store current state in history before deletion
        $historyData = $branch->toArray();
        unset($historyData['id']);
        $historyData['is_active']=false;
                    $historyData["company_info_id"] =$branch->company_id;

        if (!empty($branch['image']))
                $historyData['image'] = $branch['image'];
        CompanyBranchesHistory::create($historyData);

        $branch->delete();

        return redirect()->route('companyBranch.index')->with('success', 'Company branch deleted successfully.');
    }
}
