<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Newest 10 orders (with user)
        $newOrders = Order::with('user')
            ->latest('created_at')
            ->take(10)
            ->get();

        // Completed orders:
        // If the model has scopeCompleted (i.e., method scopeCompleted), use it.
        // Otherwise, fall back to status comparison.
        $completedQuery = method_exists(Order::class, 'scopeCompleted')
            ? Order::completed()
            : Order::where('status', defined('App\Models\Order::STATUS_COMPLETED') ? Order::STATUS_COMPLETED : 'completed');

        $completedOrders = $completedQuery
            ->with('user')
            ->latest('created_at')
            ->take(10)
            ->get();

        // Newest 10 users (pick only fields you show)
        $newUsers = User::select(['id', 'email', 'created_at'])
            ->latest('created_at')
            ->take(10)
            ->get();

        // Newest 10 products (adjust fields to your schema)
        $newProducts = Product::select(['id', 'name_en','name_ar', 'price', 'created_at'])
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('home', compact(
            'newOrders',
            'completedOrders',
            'newUsers',
            'newProducts'
        ));
    }
}
