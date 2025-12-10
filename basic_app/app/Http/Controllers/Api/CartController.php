<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use Illuminate\Http\Request;

use App\Http\Requests\QuantityRequest;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Return full cart for the authenticated user (with product + size).
     */
    public function index(): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        return $this->buildCartResponse($userId, 'Cart retrieved.');
    }

    /**
     * Add item to cart, then return the updated cart (same as index).
     */
    public function addToCart(CartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        // Check if item already exists in cart for this user (same product + color + size)
        $exists = Cart::where('user_id', $userId)
            ->where('product_id', $validated['product_id'])
            ->when(isset($validated['color']), fn($q) => $q->where('color', $validated['color']))
            ->when(isset($validated['size_id']), fn($q) => $q->where('size_id', $validated['size_id']))
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'exists',
                'message' => 'Product already added to cart.',
                'data'    => [],
            ], 409); // 409 Conflict is clearer than 403 here
        }

        $validated['user_id'] = $userId;
        Cart::create($validated);

        // 🔥 After adding, return the same data format as index (updated cart)
        return $this->buildCartResponse($userId, 'Product added to cart.', 201);
    }

    /**
     * Update quantity of a cart item (only for authenticated user).
     */
    public function updateQuantity(QuantityRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        $cartItem = Cart::where('id', $validated['id'])
            ->where('user_id', $userId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Cart item not found.',
                'data'    => [],
            ], 404);
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],
        ]);

        // You can return just the item OR the full cart; here I return full cart:
        return $this->buildCartResponse($userId, 'Cart item quantity updated.');
    }

    /**
     * Remove an item from cart and return the updated cart.
     */
      public function removeFromCart(Request $request): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        $cartItem = Cart::where('id', $request->id)
            ->where('user_id', $userId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Cart item not found.',
                'data'    => [],
            ], 404);
        }

        $cartItem->delete();

        return $this->buildCartResponse($userId, 'Product removed from cart.');
    }


    /**
     * Helper: build a unified cart response (used by index/add/update/remove).
     */
    protected function buildCartResponse(
        int $userId,
        string $message = 'Cart retrieved.',
        int $statusCode = 200
    ): JsonResponse {
        $cart = Cart::with(['product', 'size'])
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'status'  => 'ok',
            'message' => $message,
            'data'    => $cart,
        ], $statusCode);
    }
}
