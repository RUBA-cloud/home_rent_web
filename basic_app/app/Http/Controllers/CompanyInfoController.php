<?php

namespace App\Http\Controllers;

use App\Events\CompanyInfoEventSent;
use App\Http\Requests\CompanyInfoRequest;
use App\Models\CompanyInfo;
use App\Models\CompanyInfoHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the current company information.
     */
    public function index($isHistory =false)
    {
        if($isHistory){

        $history = CompanyInfoHistory::with('user')
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('company_info.history', compact('history'));}

        $company = CompanyInfo::query()->first();
        return view('company_info.index', [
            'company' => $company,
        ]);
    }
 public function history(){

        $history = CompanyInfoHistory::with('user')
            ->orderByDesc('created_at')
            ->paginate(5);
                    return view('company_info.history', compact('history'));}



    public function searchHistory(Request $request)
    {
        $searchTerm = (string) $request->input('search', '');

        $history = CompanyInfoHistory::with('user')
            ->when($searchTerm !== '', function ($q) use ($searchTerm) {
                $q->where(function ($q2) use ($searchTerm) {
                    $like = '%' . $searchTerm . '%';

                    $q2->where('name_en', 'like', $like)
                        ->orWhere('name_ar', 'like', $like)
                        ->orWhere('address_en', 'like', $like)
                        ->orWhere('address_ar', 'like', $like)
                        ->orWhere('about_us_en', 'like', $like)
                        ->orWhere('about_us_ar', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends(['search' => $searchTerm]); // keep query in pagination links

        return view('company_info.history', compact('history', 'searchTerm'));
    }

    /**
     * Show a specific history entry.
     */
    public function show($companyInfo)
    {
        $entry = CompanyInfoHistory::with('user')->findOrFail($companyInfo);

        // Make sure your blade is at: resources/views/company_info/show.blade.php
        return view('company_info.show', ['company' => $entry]);
    }

    /**
     * Store or update company info, keep previous in history, and broadcast after commit.
     */
    public function store(CompanyInfoRequest $request)
    {
        $validated = $request->validated();

        // Handle image upload (optional)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('company_logos', 'public');
            // Save a public URL for easy display
            $validated['image'] = asset('storage/' . $path);
        }

        $savedCompany = null;

        DB::transaction(function () use (&$savedCompany, $validated) {
            $company = CompanyInfo::query()->first();

            if ($company) {
                // Save current snapshot to history
                $historyData = $company->toArray();
                unset($historyData['id']);         // avoid collisions
                $historyData['user_id'] = Auth::id();

                CompanyInfoHistory::create($historyData);

                // Update current record
                $company->update($validated);
                $savedCompany = $company->fresh();
            } else {
                // Create new company
                $savedCompany = CompanyInfo::create($validated);

                // Also record initial snapshot in history
                $historyData = $savedCompany->toArray();
                unset($historyData['id']);
                $historyData['user_id'] = Auth::id();

                CompanyInfoHistory::create($historyData);
            }
        });
        // Broadcast AFTER the transaction succeeds
        if ($savedCompany) {
    // or:
            try {
                broadcast(new CompanyInfoEventSent($savedCompany));


   } catch (\Throwable $e) {
                Log::warning('CompanyInfoEventSent broadcast failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Company information saved successfully.');
    }
}
