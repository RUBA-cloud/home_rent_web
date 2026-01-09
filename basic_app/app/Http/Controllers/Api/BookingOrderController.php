<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\BookingUpdateRequest;
use App\Models\BookingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingOrderController extends Controller
{
    // ✅ Store a new booking
    public function store(BookingRequest $request)
    {
        $validated = $request->validated();
           $validated['children_count'] = (int) ($validated['children_count'] ?? 0);


        // ✅ force current user
        $validated['user_id'] = Auth::id();
        $booking = BookingModel::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Booking created successfully',
            'data'    => $booking->load(['user', 'home']),
        ], 201);
    }

    // ✅ My bookings
    public function index(Request $request)
    {
        $bookings = BookingModel::with(['user', 'home'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $bookings,
        ], 200);
    }

    // ✅ Update booking
    public function update(BookingUpdateRequest $request)
    {
        $validated = $request->validated();

        // ✅ owner check + not found handling
        $booking = BookingModel::where('id', $validated['booking_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // ✅ do NOT update booking_id or user_id
        unset($validated['booking_id']);
        unset($validated['user_id']); // just in case

        // ✅ update only allowed fields (optional but recommended)
        $allowed = [
            'home_id',
            'from_date',
            'end_date',
            'adults_count',
            'childern_count',
        ];

        $dataToUpdate = array_intersect_key($validated, array_flip($allowed));

        $booking->update($dataToUpdate);

        return response()->json([
            'status'  => true,
            'message' => 'Booking updated successfully',
            'data'    => $booking->fresh()->load(['user', 'home']),
        ], 200);
    }

    // ✅ Delete booking
    public function destroy($id)
    {
        $booking = BookingModel::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $booking->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Booking deleted successfully',
        ], 200);
    }
}
