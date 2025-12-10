<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\CompanyInfoEventSent;
use App\Models\CompanyBranch;
use Illuminate\Http\JsonResponse;
use Throwable;

class CompanyBranchController extends Controller
{
    public function index(): JsonResponse
    {
$branches = CompanyBranch::where('is_active', 1)
    ->orderByDesc('id')
    ->paginate(10)
    ->appends(request()->query());

        if (!$branches) {
            return response()->json([
                'status'  => 'not_found',
                'message' =>'No Company branches found',
            ], 404);
        }

        // Try to broadcast (non-fatal if broadcasting isn't configured)
        try {
           // event(new CompanyInfoEventSent($company));
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'ok_no_broadcast',
                'message' => 'Company found, but broadcasting failed or is not configured.',
                'branches' => $branches,
            ], 200);
        }

        return response()->json([
            'status'  => 'ok',
            'message' => 'Broadcast sent.',
            'company' => $branches,
        ], 200);
    }
}
