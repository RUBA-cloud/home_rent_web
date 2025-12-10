<?php
// app/Http/Controllers/DeviceTokenController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $r)
    {
        $r->validate(['token' => 'required|string', 'platform' => 'nullable|string']);
        $r->user()->deviceTokens()->updateOrCreate(
            ['token' => $r->token],
            ['platform' => $r->input('platform', 'web')]
        );
        return response()->noContent();
    }
}