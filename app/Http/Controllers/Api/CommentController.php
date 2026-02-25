<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Get all comments for a recipe
     */
    public function index($recipeId)
    {
        $comments = $this->commentService->getRecipeComments($recipeId);

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
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        try {
            $comment = $this->commentService->addComment($recipeId, Auth::user(), $validated);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => $comment
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 400);
        }
    }

    /**
     * Update a comment
     */
    public function update(Request $request, $recipeId, $commentId)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        $comment = $this->commentService->updateComment($commentId, $recipeId, Auth::id(), $validated);

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
        $this->commentService->deleteComment($commentId, $recipeId, Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
