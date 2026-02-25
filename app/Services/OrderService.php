<?php

namespace App\Services;

use App\Models\Order;
use App\Models\StoreProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Get user's orders.
     */
    public function getUserOrders(int $userId): Collection
    {
        return Order::with(['store.user', 'storeProduct.recipe'])
            ->where('customer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new order.
     */
    public function createOrder(int $customerId, array $data): Order
    {
        $storeProduct = StoreProduct::with(['store', 'recipe'])
            ->where('is_available', true)
            ->findOrFail($data['store_product_id']);

        if ($storeProduct->quantity_available < $data['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => ['Insufficient quantity available']
            ]);
        }

        $distance = $this->calculateDistance(
            $storeProduct->store->latitude,
            $storeProduct->store->longitude,
            $data['delivery_latitude'],
            $data['delivery_longitude']
        );

        if ($distance > 10) {
            throw ValidationException::withMessages([
                'delivery_latitude' => ['Delivery location is too far (more than 10km from store)']
            ]);
        }

        return DB::transaction(function () use ($customerId, $storeProduct, $data) {
            $totalPrice = $storeProduct->price * $data['quantity'];
            
            $order = Order::create([
                'customer_id' => $customerId,
                'store_id' => $storeProduct->store_id,
                'store_product_id' => $storeProduct->id,
                'quantity' => $data['quantity'],
                'total_price' => $totalPrice,
                'payment_method' => $data['payment_method'],
                'delivery_address' => $data['delivery_address'],
                'delivery_latitude' => $data['delivery_latitude'],
                'delivery_longitude' => $data['delivery_longitude'],
                'notes' => $data['notes'] ?? null
            ]);

            $storeProduct->decrement('quantity_available', $data['quantity']);

            return $order->load(['store.user', 'storeProduct.recipe']);
        });
    }

    /**
     * Get details of a specific order.
     */
    public function getOrderDetails(int $id, int $userId): Order
    {
        return Order::with(['store.user', 'storeProduct.recipe'])
            ->where('customer_id', $userId)
            ->findOrFail($id);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(int $id, int $userId, array $data): Order
    {
        $order = Order::whereHas('store', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($id);

        $order->update($data);

        return $order->load(['customer', 'storeProduct.recipe']);
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(int $id, int $userId): void
    {
        $order = Order::where('customer_id', $userId)
            ->where('status', 'pending')
            ->findOrFail($id);

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);
            $order->storeProduct->increment('quantity_available', $order->quantity);
        });
    }

    /**
     * Calculate distance between two points.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; 
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}
