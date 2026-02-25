<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class StoreService
{
    /**
     * Get stores near a location.
     */
    public function getNearbyStores(?float $lat = null, ?float $lng = null, float $radius = 10): Collection
    {
        $query = Store::with(['user', 'availableProducts.recipe'])
            ->where('is_active', true);

        if ($lat && $lng) {
            $query->whereRaw("
                (6371 * acos(cos(radians(?)) 
                * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) 
                + sin(radians(?)) 
                * sin(radians(latitude)))) <= ?
            ", [$lat, $lng, $lat, $radius]);
        }

        return $query->get();
    }

    /**
     * Create a new store for a user.
     */
    public function createStore(int $userId, array $data): Store
    {
        // Check if user already has a store
        if (Store::where('user_id', $userId)->exists()) {
            throw ValidationException::withMessages([
                'store' => ['You already have a store']
            ]);
        }

        return Store::create(array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Get details of a specific store.
     */
    public function getStoreDetails(int $id): Store
    {
        return Store::with(['user', 'availableProducts.recipe'])
            ->where('is_active', true)
            ->findOrFail($id);
    }

    /**
     * Update an existing store.
     */
    public function updateStore(int $id, int $userId, array $data): Store
    {
        $store = Store::where('user_id', $userId)->findOrFail($id);
        $store->update($data);
        return $store->load('user');
    }

    /**
     * Get user's own store.
     */
    public function getUserStore(int $userId): ?Store
    {
        return Store::with(['user', 'products.recipe'])
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get orders for a user's store.
     */
    public function getStoreOrders(int $userId): Collection
    {
        $store = Store::where('user_id', $userId)->firstOrFail();

        return $store->orders()
            ->with(['customer', 'storeProduct.recipe'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
