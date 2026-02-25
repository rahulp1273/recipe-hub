<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\RecipeComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CommentService
{
    /**
     * Get all comments for a recipe.
     */
    public function getRecipeComments(int $recipeId): Collection
    {
        $recipe = Recipe::findOrFail($recipeId);
        
        return $recipe->comments()
            ->with('user:id,name,avatar_path')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Add a comment to a recipe.
     */
    public function addComment(int $recipeId, User $user, array $data): RecipeComment
    {
        // Check if user already commented on this recipe
        $existingComment = RecipeComment::where('user_id', $user->id)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($existingComment) {
            throw ValidationException::withMessages([
                'comment' => ['You have already commented on this recipe']
            ]);
        }

        $comment = RecipeComment::create([
            'user_id' => $user->id,
            'recipe_id' => $recipeId,
            'comment' => $data['comment'],
            'rating' => $data['rating'] ?? null
        ]);

        return $comment->load('user:id,name,avatar_path');
    }

    /**
     * Update an existing comment.
     */
    public function updateComment(int $commentId, int $recipeId, int $userId, array $data): RecipeComment
    {
        $comment = RecipeComment::where('id', $commentId)
            ->where('recipe_id', $recipeId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $comment->update([
            'comment' => $data['comment'],
            'rating' => $data['rating'] ?? $comment->rating
        ]);

        return $comment->load('user:id,name,avatar_path');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(int $commentId, int $recipeId, int $userId): bool
    {
        $comment = RecipeComment::where('id', $commentId)
            ->where('recipe_id', $recipeId)
            ->where('user_id', $userId)
            ->firstOrFail();

        return $comment->delete();
    }
}
