<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\RecipeComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Get all comments for a recipe
     */
    public function index($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);
        
        $comments = $recipe->comments()
            ->with('user:id,name,avatar_path')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

    /**
     * Add a comment to a recipe
     */
    public function store(Request $request, $recipeId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        $recipe = Recipe::findOrFail($recipeId);
        $user = Auth::user();

        // Check if user already commented on this recipe
        $existingComment = RecipeComment::where('user_id', $user->id)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($existingComment) {
            return response()->json([
                'success' => false,
                'message' => 'You have already commented on this recipe'
            ], 400);
        }

        $comment = RecipeComment::create([
            'user_id' => $user->id,
            'recipe_id' => $recipeId,
            'comment' => $request->comment,
            'rating' => $request->rating
        ]);

        // Load the user relationship for the response
        $comment->load('user:id,name,avatar_path');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => $comment
        ], 201);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, $recipeId, $commentId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        $comment = RecipeComment::where('id', $commentId)
            ->where('recipe_id', $recipeId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $comment->update([
            'comment' => $request->comment,
            'rating' => $request->rating
        ]);

        $comment->load('user:id,name,avatar_path');

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => $comment
        ]);
    }

    /**
     * Delete a comment
     */
    public function destroy($recipeId, $commentId)
    {
        $comment = RecipeComment::where('id', $commentId)
            ->where('recipe_id', $recipeId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
