<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    /**
     * Display a listing of stores near the user
     */
    public function index(Request $request)
    {
        $userLat = $request->input('latitude');
        $userLng = $request->input('longitude');
        $radius = 10; // 10km radius

        $query = Store::with(['user', 'availableProducts.recipe'])
            ->where('is_active', true);

        // If user location is provided, filter by distance
        if ($userLat && $userLng) {
            $query->whereRaw("
                (6371 * acos(cos(radians(?)) 
                * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) 
                + sin(radians(?)) 
                * sin(radians(latitude)))) <= ?
            ", [$userLat, $userLng, $userLat, $radius]);
        }

        $stores = $query->get();

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

        // Check if user already has a store
        $existingStore = Store::where('user_id', Auth::id())->first();
        if ($existingStore) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a store'
            ], 400);
        }

        $store = Store::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'phone' => $request->phone,
            'email' => $request->email,
            'image_url' => $request->image_url
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Store created successfully',
            'data' => $store->load('user')
        ], 201);
    }

    /**
     * Display the specified store
     */
    public function show(string $id)
    {
        $store = Store::with(['user', 'availableProducts.recipe'])
            ->where('is_active', true)
            ->findOrFail($id);

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
        $store = Store::where('user_id', Auth::id())->findOrFail($id);

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

        $store->update($request->only([
            'name', 'description', 'address', 'latitude', 
            'longitude', 'phone', 'email', 'image_url', 'is_active'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully',
            'data' => $store->load('user')
        ]);
    }

    /**
     * Get user's own store
     */
    public function myStore()
    {
        $store = Store::with(['user', 'products.recipe'])
            ->where('user_id', Auth::id())
            ->first();

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
        $store = Store::where('user_id', Auth::id())->first();
        
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'No store found'
            ], 404);
        }

        $orders = $store->orders()
            ->with(['customer', 'storeProduct.recipe'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
