<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    protected StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * Display a listing of stores near the user
     */
    public function index(Request $request)
    {
        $stores = $this->storeService->getNearbyStores(
            $request->input('latitude'), 
            $request->input('longitude')
        );

        return response()->json([
            'success' => true,
            'data' => $stores
        ]);
    }

    /**
     * Create a new store
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'image_url' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $store = $this->storeService->createStore(Auth::id(), $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Store created successfully',
                'data' => $store->load('user')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified store
     */
    public function show(string $id)
    {
        $store = $this->storeService->getStoreDetails($id);

        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }

    /**
     * Update the specified store
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'sometimes|required|string',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'image_url' => 'nullable|url',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $store = $this->storeService->updateStore($id, Auth::id(), $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully',
            'data' => $store
        ]);
    }

    /**
     * Get user's own store
     */
    public function myStore()
    {
        $store = $this->storeService->getUserStore(Auth::id());

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'No store found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }

    /**
     * Get store orders
     */
    public function orders()
    {
        $orders = $this->storeService->getStoreOrders(Auth::id());

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
