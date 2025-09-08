<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreProductController extends Controller
{
    /**
     * Display a listing of store products
     */
    public function index(Request $request)
    {
        $storeId = $request->input('store_id');
        
        $query = StoreProduct::with(['store.user', 'recipe'])
            ->where('is_available', true);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $products = $query->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Add a recipe as a store product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipe_id' => 'required|exists:recipes,id',
            'price' => 'required|numeric|min:0.01',
            'quantity_available' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'preparation_time' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user has a store
        $store = Store::where('user_id', Auth::id())->first();
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'You need to create a store first'
            ], 400);
        }

        // Check if recipe belongs to the user
        $recipe = Recipe::where('id', $request->recipe_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$recipe) {
            return response()->json([
                'success' => false,
                'message' => 'Recipe not found or you do not own this recipe'
            ], 404);
        }

        // Check if this recipe is already a product in this store
        $existingProduct = StoreProduct::where('store_id', $store->id)
            ->where('recipe_id', $request->recipe_id)
            ->first();

        if ($existingProduct) {
            return response()->json([
                'success' => false,
                'message' => 'This recipe is already added to your store'
            ], 400);
        }

        $product = StoreProduct::create([
            'store_id' => $store->id,
            'recipe_id' => $request->recipe_id,
            'price' => $request->price,
            'quantity_available' => $request->quantity_available,
            'description' => $request->description,
            'preparation_time' => $request->preparation_time
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to store successfully',
            'data' => $product->load(['store', 'recipe'])
        ], 201);
    }

    /**
     * Display the specified store product
     */
    public function show(string $id)
    {
        $product = StoreProduct::with(['store.user', 'recipe'])
            ->where('is_available', true)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified store product
     */
    public function update(Request $request, string $id)
    {
        $product = StoreProduct::whereHas('store', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'price' => 'sometimes|required|numeric|min:0.01',
            'quantity_available' => 'sometimes|required|integer|min:0',
            'description' => 'nullable|string',
            'preparation_time' => 'nullable|string|max:100',
            'is_available' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->only([
            'price', 'quantity_available', 'description', 
            'preparation_time', 'is_available'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load(['store', 'recipe'])
        ]);
    }

    /**
     * Remove the specified store product
     */
    public function destroy(string $id)
    {
        $product = StoreProduct::whereHas('store', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from store successfully'
        ]);
    }

    /**
     * Get user's store products
     */
    public function myProducts()
    {
        $store = Store::where('user_id', Auth::id())->first();
        
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'No store found'
            ], 404);
        }

        $products = $store->products()
            ->with('recipe')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
