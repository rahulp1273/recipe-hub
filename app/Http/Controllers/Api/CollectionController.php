<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecipeCollection;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Get all collections for the authenticated user
     */
    public function index()
    {
        $collections = RecipeCollection::where('user_id', Auth::id())
            ->with(['recipes'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($collection) {
                $collection->recipes_count = $collection->recipes->count();
                return $collection;
            });

        return response()->json([
            'success' => true,
            'data' => $collections
        ]);
    }

    /**
     * Get a specific collection with its recipes
     */
    public function show($id)
    {
        $collection = RecipeCollection::where('user_id', Auth::id())
            ->with(['recipes.user', 'recipes.comments'])
            ->findOrFail($id);

        $collection->recipes_count = $collection->recipes->count();

        return response()->json([
            'success' => true,
            'data' => $collection
        ]);
    }

    /**
     * Create a new collection
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:10'
        ]);

        $collection = RecipeCollection::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_public' => $validated['is_public'] ?? false,
            'color' => $validated['color'] ?? '#FF6B35',
            'icon' => $validated['icon'] ?? '📁'
        ]);

        $collection->recipes_count = 0;

        return response()->json([
            'success' => true,
            'message' => 'Collection created successfully!',
            'data' => $collection
        ], 201);
    }

    /**
     * Update a collection
     */
    public function update(Request $request, $id)
    {
        $collection = RecipeCollection::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:10'
        ]);

        $collection->update($validated);

        $collection->recipes_count = $collection->recipes()->count();

        return response()->json([
            'success' => true,
            'message' => 'Collection updated successfully!',
            'data' => $collection
        ]);
    }

    /**
     * Delete a collection
     */
    public function destroy($id)
    {
        $collection = RecipeCollection::where('user_id', Auth::id())->findOrFail($id);
        $collection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collection deleted successfully!'
        ]);
    }

    /**
     * Add a recipe to a collection
     */
    public function addRecipe(Request $request, $collectionId)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id'
        ]);

        $collection = RecipeCollection::where('user_id', Auth::id())->findOrFail($collectionId);
        
        // Check if recipe is already in collection
        if ($collection->recipes()->where('recipe_id', $request->recipe_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Recipe is already in this collection'
            ], 400);
        }

        $collection->recipes()->attach($request->recipe_id);

        return response()->json([
            'success' => true,
            'message' => 'Recipe added to collection successfully!'
        ]);
    }

    /**
     * Remove a recipe from a collection
     */
    public function removeRecipe(Request $request, $collectionId)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id'
        ]);

        $collection = RecipeCollection::where('user_id', Auth::id())->findOrFail($collectionId);
        $collection->recipes()->detach($request->recipe_id);

        return response()->json([
            'success' => true,
            'message' => 'Recipe removed from collection successfully!'
        ]);
    }

    /**
     * Get public collections
     */
    public function publicCollections()
    {
        $collections = RecipeCollection::where('is_public', true)
            ->with(['user:id,name', 'recipes'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Add recipes_count to each collection
        $collections->getCollection()->transform(function ($collection) {
            $collection->recipes_count = $collection->recipes->count();
            return $collection;
        });

        return response()->json([
            'success' => true,
            'data' => $collections
        ]);
    }
}
