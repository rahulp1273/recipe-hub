<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of user's orders
     */
    public function index()
    {
        $orders = $this->orderService->getUserOrders(Auth::id());

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

        try {
            $order = $this->orderService->createOrder(Auth::id(), $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
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
        $order = $this->orderService->getOrderDetails($id, Auth::id());

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

        $order = $this->orderService->updateOrderStatus($id, Auth::id(), $request->only(['status', 'delivery_time']));

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }

    /**
     * Cancel an order (for customers)
     */
    public function cancel(string $id)
    {
        try {
            $this->orderService->cancelOrder($id, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }
}
