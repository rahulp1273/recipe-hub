<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\RecipeLike;
use App\Models\RecipeView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SocialController extends Controller
{
    // Get public recipe feed
    public function getFeed(Request $request)
    {
        try {
            $recipes = Recipe::with(['user:id,name,email', 'likes'])
                ->where('is_public', true)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Add like status for current user
            $user = Auth::user();
            $recipes->getCollection()->transform(function ($recipe) use ($user) {
                $recipe->is_liked = $recipe->isLikedBy($user);
                $recipe->likes_count = $recipe->likes()->count();
                return $recipe;
            });

            return response()->json([
                'success' => true,
                'data' => $recipes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch feed'
            ], 500);
        }
    }

    // Like/Unlike recipe
    public function toggleLike(Request $request, $recipeId)
    {
        try {
            $user = Auth::user();
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

            return response()->json([
                'success' => true,
                'is_liked' => $isLiked,
                'likes_count' => $recipe->fresh()->likes_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle like'
            ], 500);
        }
    }

    // Record recipe view
    public function recordView(Request $request, $recipeId)
    {
        try {
            $user = Auth::user();
            $recipe = Recipe::findOrFail($recipeId);

            // Check if user already viewed this recipe today
            $today = now()->startOfDay();
            $existingView = RecipeView::where('recipe_id', $recipeId)
                ->where(function ($query) use ($user, $request) {
                    if ($user) {
                        $query->where('user_id', $user->id);
                    } else {
                        $query->where('ip_address', $request->ip());
                    }
                })
                ->where('created_at', '>=', $today)
                ->first();

            if (!$existingView) {
                // Record new view
                RecipeView::create([
                    'user_id' => $user ? $user->id : null,
                    'recipe_id' => $recipeId,
                    'ip_address' => $request->ip()
                ]);

                $recipe->increment('views_count');
            }

            return response()->json([
                'success' => true,
                'views_count' => $recipe->fresh()->views_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record view'
            ], 500);
        }
    }

    // Get recipe stats
    public function getRecipeStats($recipeId)
    {
        try {
            $recipe = Recipe::findOrFail($recipeId);
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'data' => [
                    'likes_count' => $recipe->likes_count,
                    'views_count' => $recipe->views_count,
                    'is_liked' => $recipe->isLikedBy($user)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats'
            ], 500);
        }
    }
}
