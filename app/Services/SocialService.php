<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\RecipeLike;
use App\Models\RecipeView;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SocialService
{
    /**
     * Get public recipe feed with filters.
     */
    public function getFeed(array $filters, ?User $user = null): LengthAwarePaginator
    {
        $query = Recipe::with(['user:id,name,email'])
            ->where('is_public', true);

        // Search (title, description, ingredients, user name)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('ingredients', 'like', "%$search%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%") ;
                  });
            });
        }

        // Category filter
        $category = $filters['category'] ?? null;
        $type = $filters['type'] ?? null;
        if ($category) {
            $query->where('category', $category);
        }

        // Type filter (veg/non-veg)
        if ($type) {
            if (!$category) {
                if ($type === 'vegetarian') {
                    $query->where('category', 'vegetarian');
                } elseif ($type === 'non-vegetarian') {
                    $query->where('category', 'non-vegetarian');
                }
            } else if (in_array($category, ['vegetarian', 'non-vegetarian']) && $category === $type) {
                $query->where('category', $type);
            }
        }

        // Min rating filter
        if (!empty($filters['min_rating'])) {
            $minRating = (int) $filters['min_rating'];
            $query->where('rating', '>=', $minRating);
        }

        // Max prep time filter
        if (!empty($filters['max_prep_time'])) {
            $maxPrep = (int) $filters['max_prep_time'];
            $query->where('prep_time', '<=', $maxPrep);
        }

        $recipes = $query->orderBy('created_at', 'desc')->paginate(10);

        // Add like status for current user
        $recipes->getCollection()->transform(function ($recipe) use ($user) {
            $recipe->is_liked = $user ? $recipe->isLikedBy($user) : false;
            return $recipe;
        });

        return $recipes;
    }

    /**
     * Toggle like/unlike for a recipe.
     */
    public function toggleLike(int $recipeId, User $user): array
    {
        $recipe = Recipe::findOrFail($recipeId);

        $existingLike = RecipeLike::where('user_id', $user->id)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $recipe->decrement('likes_count');
            $isLiked = false;
        } else {
            // Like
            RecipeLike::create([
                'user_id' => $user->id,
                'recipe_id' => $recipeId
            ]);
            $recipe->increment('likes_count');
            $isLiked = true;
        }

        return [
            'is_liked' => $isLiked,
            'likes_count' => $recipe->fresh()->likes_count
        ];
    }

    /**
     * Record a view for a recipe.
     */
    public function recordView(int $recipeId, ?User $user = null, ?string $ipAddress = null): int
    {
        $recipe = Recipe::findOrFail($recipeId);
        $today = now()->startOfDay();

        $existingView = RecipeView::where('recipe_id', $recipeId)
            ->where(function ($query) use ($user, $ipAddress) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->where('created_at', '>=', $today)
            ->first();

        if (!$existingView) {
            RecipeView::create([
                'user_id' => $user ? $user->id : null,
                'recipe_id' => $recipeId,
                'ip_address' => $ipAddress
            ]);

            $recipe->increment('views_count');
        }

        return $recipe->fresh()->views_count;
    }

    /**
     * Get recipe statistics.
     */
    public function getRecipeStats(int $recipeId, ?User $user = null): array
    {
        $recipe = Recipe::findOrFail($recipeId);

        return [
            'likes_count' => $recipe->likes_count,
            'views_count' => $recipe->views_count,
            'is_liked' => $user ? $recipe->isLikedBy($user) : false
        ];
    }
}
