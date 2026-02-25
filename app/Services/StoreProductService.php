<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Store;
use App\Models\StoreProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class StoreProductService
{
    /**
     * Get available store products.
     */
    public function getAvailableProducts(?int $storeId = null): Collection
    {
        $query = StoreProduct::with(['store.user', 'recipe'])
            ->where('is_available', true);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->get();
    }

    /**
     * Add a recipe as a store product.
     */
    public function addProduct(int $userId, array $data): StoreProduct
    {
        $store = Store::where('user_id', $userId)->first();
        if (!$store) {
            throw ValidationException::withMessages([
                'store' => ['You need to create a store first']
            ]);
        }

        $recipe = Recipe::where('id', $data['recipe_id'])
            ->where('user_id', $userId)
            ->first();

        if (!$recipe) {
            throw ValidationException::withMessages([
                'recipe_id' => ['Recipe not found or you do not own this recipe']
            ]);
        }

        if (StoreProduct::where('store_id', $store->id)->where('recipe_id', $data['recipe_id'])->exists()) {
            throw ValidationException::withMessages([
                'recipe_id' => ['This recipe is already added to your store']
            ]);
        }

        return StoreProduct::create(array_merge($data, ['store_id' => $store->id]));
    }

    /**
     * Get details of a specific product.
     */
    public function getProductDetails(int $id): StoreProduct
    {
        return StoreProduct::with(['store.user', 'recipe'])
            ->where('is_available', true)
            ->findOrFail($id);
    }

    /**
     * Update an existing store product.
     */
    public function updateProduct(int $id, int $userId, array $data): StoreProduct
    {
        $product = StoreProduct::whereHas('store', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($id);

        $product->update($data);

        return $product->load(['store', 'recipe']);
    }

    /**
     * Delete a store product.
     */
    public function deleteProduct(int $id, int $userId): bool
    {
        $product = StoreProduct::whereHas('store', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($id);

        return $product->delete();
    }

    /**
     * Get products for a user's store.
     */
    public function getUserProducts(int $userId): Collection
    {
        $store = Store::where('user_id', $userId)->firstOrFail();

        return $store->products()
            ->with('recipe')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
