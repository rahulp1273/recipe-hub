<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    /**
     * Display a listing of recipes
     */
    public function index(): JsonResponse
    {
        $recipes = Recipe::with('user')
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    }

    /**
     * Store a newly created recipe
     */
    public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'nullable|string|max:100',
        'prep_time' => 'nullable|integer|min:0',
        'cook_time' => 'nullable|integer|min:0',
        'servings' => 'nullable|integer|min:1',
        'ingredients' => 'required|array|min:1',
        'ingredients.*' => 'required|string',
        'instructions' => 'required|array|min:1',
        'instructions.*' => 'required|string',
    ]);

    $recipe = Recipe::create([
        'user_id' => Auth::id(),
        'title' => $validated['title'],
        'slug' => Str::slug($validated['title']), // Add this line
        'description' => $validated['description'],
        'category' => $validated['category'],
        'prep_time' => $validated['prep_time'],
        'cook_time' => $validated['cook_time'],
        'servings' => $validated['servings'],
        'ingredients' => $validated['ingredients'], // Remove json_encode
        'instructions' => $validated['instructions'], // Remove json_encode
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Recipe created successfully!',
        'data' => $recipe->load('user')
    ], 201);
}


    /**
     * Display the specified recipe
     */
    public function show(Recipe $recipe): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $recipe->load('user')
        ]);
    }

    /**
     * Update the specified recipe
     */
    public function update(Request $request, Recipe $recipe): JsonResponse
    {
        // Check if user owns the recipe
        if ($recipe->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|string',
            'instructions' => 'required|array|min:1',
            'instructions.*' => 'required|string',
        ]);

        $recipe->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'prep_time' => $validated['prep_time'],
            'cook_time' => $validated['cook_time'],
            'servings' => $validated['servings'],
            'ingredients' => json_encode($validated['ingredients']),
            'instructions' => json_encode($validated['instructions']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recipe updated successfully!',
            'data' => $recipe->load('user')
        ]);
    }

    /**
     * Remove the specified recipe
     */
    public function destroy(Recipe $recipe): JsonResponse
    {
        // Check if user owns the recipe
        if ($recipe->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $recipe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recipe deleted successfully!'
        ]);
    }

    /**
     * Get user's recipes
     */
    public function myRecipes(): JsonResponse
    {
        $recipes = Recipe::where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    }
}
