<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SocialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialController extends Controller
{
    protected SocialService $socialService;

    public function __construct(SocialService $socialService)
    {
        $this->socialService = $socialService;
    }

    // Get public recipe feed
    public function getFeed(Request $request)
    {
        try {
            $recipes = $this->socialService->getFeed($request->all(), Auth::user());

            return response()->json([
                'success' => true,
                'data' => $recipes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch feed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Like/Unlike recipe
    public function toggleLike(Request $request, $recipeId)
    {
        try {
            $result = $this->socialService->toggleLike($recipeId, Auth::user());

            return response()->json([
                'success' => true,
                'is_liked' => $result['is_liked'],
                'likes_count' => $result['likes_count']
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
            $viewsCount = $this->socialService->recordView($recipeId, Auth::user(), $request->ip());

            return response()->json([
                'success' => true,
                'views_count' => $viewsCount
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
            $stats = $this->socialService->getRecipeStats($recipeId, Auth::user());

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats'
            ], 500);
        }
    }
}
