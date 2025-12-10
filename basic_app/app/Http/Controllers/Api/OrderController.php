<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * List orders for the authenticated user (as JSON).
     */
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        $orders = Order::with([
                'items.product',
                'offer',
                'employee',
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Orders list.',
            'data'    => $orders,
        ]);
    }

    /**
     * Create a new order from validated OrderRequest.
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        // All validated data from OrderRequest
        $data = $request->validated();

        // Ensure products are provided
        if (empty($data['products']) || !is_array($data['products'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No products provided for this order.',
                'data'    => null,
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($userId, $data) {
                // 1) Create base order (only order-level fields)
                $order = Order::create([
                    'user_id'         => $userId,
                    'status'          => Order::STATUS_PENDING ?? 0, // fallback to 0 if constant not defined
                    'address'         => $data['address'],
                    'street_name'     => $data['street_name'],
                    'building_number' => $data['building_number'],
                    'lat'             => $data['lat'],
                    'long'            => $data['long'],
                    // you can ignore client total_price and calculate yourself
                    'total_price'     => $data['total_price'] ?? 0,
                ]);

                $orderTotal = 0;

                // 2) Create order items from products array
                foreach ($data['products'] as $productData) {
                    // Example productData:
                    // ["product_id" => 1, "size_id" => 3, "quantity" => 2, "colors" => ["red", "blue"]]

                    $productId = $productData['product_id'];
                    $sizeId    = $productData['size_id'] ?? null;
                    $quantity  = $productData['quantity'];
                    $colors    = $productData['colors'] ?? [null];

                    $product   = Product::findOrFail($productId);
                    $price     = $product->price; // adjust column name if needed
                    $lineTotal = $price * $quantity;

                    // accumulate order total
                    $orderTotal += $lineTotal;

                    // If colors is a list, we create one row per color (same quantity/price)
                    foreach ($colors as $color) {
                        OrderItem::create([
                            'order_id'    => $order->id,
                            'product_id'  => $productId,
                            'size_id'     => $sizeId,
                            'color'       => $color,   // nullable string
                            'quantity'    => $quantity,
                            'price'       => $price,
                            'total_price' => $lineTotal,
                        ]);
                    }
                }

                // 3) Override total_price with server-calculated value
                $order->update(['total_price' => $orderTotal]);

                // 4) Clear user's cart inside the same transaction
                Cart::where('user_id', $userId)->delete();

                return $order;
            });

            // Reload relationships for the response
            $order->load(['items.product', 'offer', 'employee']);

            return response()->json([
                'status'  => 'success',
                'message' => 'Order created successfully.',
                'data'    => $order,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create order.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
