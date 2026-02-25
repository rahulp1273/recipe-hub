<?php

namespace App\Services;

use App\Models\RecipeCollection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CollectionService
{
    /**
     * Get all collections for a user.
     */
    public function getUserCollections(int $userId): Collection
    {
        return RecipeCollection::where('user_id', $userId)
            ->with(['recipes'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($collection) {
                $collection->recipes_count = $collection->recipes->count();
                return $collection;
            });
    }

    /**
     * Get a specific collection for a user.
     */
    public function getCollectionDetails(int $id, int $userId): RecipeCollection
    {
        $collection = RecipeCollection::where('user_id', $userId)
            ->with(['recipes.user', 'recipes.comments'])
            ->findOrFail($id);

        $collection->recipes_count = $collection->recipes->count();

        return $collection;
    }

    /**
     * Create a new collection.
     */
    public function createCollection(int $userId, array $data): RecipeCollection
    {
        $collection = RecipeCollection::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_public' => $data['is_public'] ?? false,
            'color' => $data['color'] ?? '#FF6B35',
            'icon' => $data['icon'] ?? '📁'
        ]);

        $collection->recipes_count = 0;

        return $collection;
    }

    /**
     * Update an existing collection.
     */
    public function updateCollection(int $id, int $userId, array $data): RecipeCollection
    {
        $collection = RecipeCollection::where('user_id', $userId)->findOrFail($id);
        $collection->update($data);
        $collection->recipes_count = $collection->recipes()->count();

        return $collection;
    }

    /**
     * Delete a collection.
     */
    public function deleteCollection(int $id, int $userId): bool
    {
        $collection = RecipeCollection::where('user_id', $userId)->findOrFail($id);
        return $collection->delete();
    }

    /**
     * Add a recipe to a collection.
     */
    public function addRecipeToCollection(int $collectionId, int $recipeId, int $userId): void
    {
        $collection = RecipeCollection::where('user_id', $userId)->findOrFail($collectionId);
        
        if ($collection->recipes()->where('recipe_id', $recipeId)->exists()) {
            throw ValidationException::withMessages([
                'recipe_id' => ['Recipe is already in this collection']
            ]);
        }

        $collection->recipes()->attach($recipeId);
    }

    /**
     * Remove a recipe from a collection.
     */
    public function removeRecipeFromCollection(int $collectionId, int $recipeId, int $userId): void
    {
        $collection = RecipeCollection::where('user_id', $userId)->findOrFail($collectionId);
        $collection->recipes()->detach($recipeId);
    }

    /**
     * Get public collections.
     */
    public function getPublicCollections(int $perPage = 12): LengthAwarePaginator
    {
        $collections = RecipeCollection::where('is_public', true)
            ->with(['user:id,name', 'recipes'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $collections->getCollection()->transform(function ($collection) {
            $collection->recipes_count = $collection->recipes->count();
            return $collection;
        });

        return $collections;
    }
}
