<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StoreProduct;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of user's orders
     */
    public function index()
    {
        $orders = Order::with(['store.user', 'storeProduct.recipe'])
            ->where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_product_id' => 'required|exists:store_products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash_on_delivery,credit_card',
            'delivery_address' => 'required|string',
            'delivery_latitude' => 'required|numeric|between:-90,90',
            'delivery_longitude' => 'required|numeric|between:-180,180',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the store product
        $storeProduct = StoreProduct::with(['store', 'recipe'])
            ->where('is_available', true)
            ->findOrFail($request->store_product_id);

        // Check if enough quantity is available
        if ($storeProduct->quantity_available < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient quantity available'
            ], 400);
        }

        // Calculate distance between store and delivery location
        $distance = $this->calculateDistance(
            $storeProduct->store->latitude,
            $storeProduct->store->longitude,
            $request->delivery_latitude,
            $request->delivery_longitude
        );

        // Check if delivery is within 10km
        if ($distance > 10) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery location is too far (more than 10km from store)'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create the order
            $totalPrice = $storeProduct->price * $request->quantity;
            
            $order = Order::create([
                'customer_id' => Auth::id(),
                'store_id' => $storeProduct->store_id,
                'store_product_id' => $storeProduct->id,
                'quantity' => $request->quantity,
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
                'delivery_address' => $request->delivery_address,
                'delivery_latitude' => $request->delivery_latitude,
                'delivery_longitude' => $request->delivery_longitude,
                'notes' => $request->notes
            ]);

            // Update product quantity
            $storeProduct->decrement('quantity_available', $request->quantity);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order->load(['store.user', 'storeProduct.recipe'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order'
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show(string $id)
    {
        $order = Order::with(['store.user', 'storeProduct.recipe'])
            ->where('customer_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Update order status (for store owners)
     */
    public function update(Request $request, string $id)
    {
        $order = Order::whereHas('store', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
            'delivery_time' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update($request->only(['status', 'delivery_time']));

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order->load(['customer', 'storeProduct.recipe'])
        ]);
    }

    /**
     * Cancel an order (for customers)
     */
    public function cancel(string $id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            // Update order status
            $order->update(['status' => 'cancelled']);

            // Restore product quantity
            $order->storeProduct->increment('quantity_available', $order->quantity);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }

    /**
     * Calculate distance between two coordinates in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}
