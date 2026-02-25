<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecipeService
{
    /**
     * Get paginated recipes.
     */
    public function getAllRecipes(int $perPage = 12): LengthAwarePaginator
    {
        return Recipe::with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new recipe.
     */
    public function createRecipe(array $data, $imageFile = null): Recipe
    {
        $imagePath = null;
        if ($imageFile) {
            $filename = 'recipe_' . Auth::id() . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imagePath = $imageFile->storeAs('recipes', $filename, 'public');
        }

        $recipe = Recipe::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'prep_time' => $data['prep_time'] ?? 0,
            'cook_time' => $data['cook_time'] ?? 0,
            'servings' => $data['servings'] ?? 1,
            'ingredients' => $data['ingredients'],
            'instructions' => $data['instructions'],
            'image' => $imagePath,
            'is_vegetarian' => $data['is_vegetarian'] ?? false,
            'is_public' => $data['is_public'] ?? true,
        ]);

        return $recipe;
    }

    /**
     * Get recipe details with extra stats.
     */
    public function getRecipeDetails(Recipe $recipe, ?User $user = null): Recipe
    {
        $recipe->load('user');
        
        // Add comments count and average rating
        $recipe->comments_count = $recipe->comments()->count();
        $recipe->average_rating = $recipe->comments()->whereNotNull('rating')->avg('rating') ?? 0;
        
        // Check if current user has liked this recipe
        $recipe->is_liked = $user ? $recipe->isLikedBy($user) : false;

        return $recipe;
    }

    /**
     * Update an existing recipe.
     */
    public function updateRecipe(Recipe $recipe, array $data, $imageFile = null, bool $deleteImage = false): Recipe
    {
        $imagePath = $recipe->image;

        if ($deleteImage) {
            if ($recipe->image) {
                Storage::disk('public')->delete($recipe->image);
                $imagePath = null;
            }
        } elseif ($imageFile) {
            if ($recipe->image) {
                Storage::disk('public')->delete($recipe->image);
            }
            $filename = 'recipe_' . Auth::id() . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imagePath = $imageFile->storeAs('recipes', $filename, 'public');
        }

        $recipe->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? $recipe->description,
            'category' => $data['category'] ?? $recipe->category,
            'prep_time' => $data['prep_time'] ?? $recipe->prep_time,
            'cook_time' => $data['cook_time'] ?? $recipe->cook_time,
            'servings' => $data['servings'] ?? $recipe->servings,
            'ingredients' => $data['ingredients'] ?? $recipe->ingredients,
            'instructions' => $data['instructions'] ?? $recipe->instructions,
            'image' => $imagePath,
            'is_vegetarian' => $data['is_vegetarian'] ?? $recipe->is_vegetarian,
            'is_public' => $data['is_public'] ?? $recipe->is_public,
        ]);

        return $recipe;
    }

    /**
     * Delete a recipe.
     */
    public function deleteRecipe(Recipe $recipe): bool
    {
        if ($recipe->image) {
            Storage::disk('public')->delete($recipe->image);
        }
        return $recipe->delete();
    }

    /**
     * Get recipes for a specific user.
     */
    public function getUserRecipes(int $userId, int $perPage = 12): LengthAwarePaginator
    {
        return Recipe::where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
