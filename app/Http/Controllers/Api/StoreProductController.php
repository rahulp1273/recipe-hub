<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StoreProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreProductController extends Controller
{
    protected StoreProductService $storeProductService;

    public function __construct(StoreProductService $storeProductService)
    {
        $this->storeProductService = $storeProductService;
    }

    /**
     * Display a listing of store products
     */
    public function index(Request $request)
    {
        $products = $this->storeProductService->getAvailableProducts($request->input('store_id'));

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

        try {
            $product = $this->storeProductService->addProduct(Auth::id(), $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product added to store successfully',
                'data' => $product->load(['store', 'recipe'])
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 400);
        }
    }

    /**
     * Display the specified store product
     */
    public function show(string $id)
    {
        $product = $this->storeProductService->getProductDetails($id);

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

        $product = $this->storeProductService->updateProduct($id, Auth::id(), $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified store product
     */
    public function destroy(string $id)
    {
        $this->storeProductService->deleteProduct($id, Auth::id());

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
        try {
            $products = $this->storeProductService->getUserProducts(Auth::id());

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No store found'
            ], 404);
        }
    }
}
