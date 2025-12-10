<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\CompanyInfoEventSent;
use App\Models\CompanyInfo;
use Illuminate\Http\JsonResponse;
use Throwable;

class CompanyInfoController extends Controller
{
    public function index(): JsonResponse
    {
        $company = CompanyInfo::query()->latest('id')->first();

        if (!$company) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'No company found.',
            ], 404);
        }

        // Try to broadcast (non-fatal if broadcasting isn't configured)
        try {
            broadcast(new CompanyInfoEventSent($company));
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'ok_no_broadcast',
                'message' => 'Company found, but broadcasting failed or is not configured.',
                'company' => $company,
            ], 200);
        }

        return response()->json([
            'status'  => 'ok',
            'message' => 'Broadcast sent.',
            'company' => $company,
        ], 200);
    }
}
