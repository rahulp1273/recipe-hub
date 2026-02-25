<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CollectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    protected CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Get all collections for the authenticated user
     */
    public function index()
    {
        $collections = $this->collectionService->getUserCollections(Auth::id());

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
        $collection = $this->collectionService->getCollectionDetails($id, Auth::id());

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

        $collection = $this->collectionService->createCollection(Auth::id(), $validated);

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:10'
        ]);

        $collection = $this->collectionService->updateCollection($id, Auth::id(), $validated);

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
        $this->collectionService->deleteCollection($id, Auth::id());

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

        try {
            $this->collectionService->addRecipeToCollection($collectionId, $request->recipe_id, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Recipe added to collection successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove a recipe from a collection
     */
    public function removeRecipe(Request $request, $collectionId)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id'
        ]);

        $this->collectionService->removeRecipeFromCollection($collectionId, $request->recipe_id, Auth::id());

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
        $collections = $this->collectionService->getPublicCollections();

        return response()->json([
            'success' => true,
            'data' => $collections
        ]);
    }
}
