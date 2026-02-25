<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    protected RecipeService $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    /**
     * Display a listing of recipes
     */
    public function index(): JsonResponse
    {
        $recipes = $this->recipeService->getAllRecipes();

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $recipe = $this->recipeService->createRecipe($validated, $request->file('image'));

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
        $recipe = $this->recipeService->getRecipeDetails($recipe, Auth::user());

        return response()->json([
            'success' => true,
            'data' => $recipe
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'delete_image' => 'sometimes|boolean',
        ]);

        $recipe = $this->recipeService->updateRecipe(
            $recipe, 
            $validated, 
            $request->file('image'), 
            $request->boolean('delete_image')
        );

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

        $this->recipeService->deleteRecipe($recipe);

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
        $recipes = $this->recipeService->getUserRecipes(Auth::id());

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    }
}
