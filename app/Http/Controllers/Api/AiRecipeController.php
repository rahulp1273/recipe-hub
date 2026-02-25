<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiRecipeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AiRecipeController extends Controller
{
    protected AiRecipeService $aiRecipeService;

    public function __construct(AiRecipeService $aiRecipeService)
    {
        $this->aiRecipeService = $aiRecipeService;
    }

    /**
     * Generate a recipe using AI
     */
    public function generateRecipe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        try {
            $recipe = $this->aiRecipeService->generateAndStoreRecipe($validated['prompt'], Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'AI Recipe generated successfully!',
                'data' => $recipe->load('user')
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Recipe Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recipe. Please try again.',
                'error' => 'Service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Get AI generation history for user
     */
    public function getAiGeneratedRecipes(): JsonResponse
    {
        $recipes = $this->aiRecipeService->getUserAiRecipes(Auth::id());

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    }
}
 