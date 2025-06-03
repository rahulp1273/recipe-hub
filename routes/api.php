<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\SocialController;

// =============================================================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================================================

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API working!']);
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public social routes (anyone can access)
Route::get('/feed', [SocialController::class, 'getFeed']);                     // Public recipe feed
Route::post('/recipes/{recipe}/view', [SocialController::class, 'recordView']); // Record recipe view

// =============================================================================
// PRIVATE ROUTES (Authentication Required)
// =============================================================================

Route::middleware('auth:sanctum')->group(function () {

    // Auth routes (authenticated users only)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Recipe routes (authenticated users only)
    Route::get('/recipes', [RecipeController::class, 'index']);                    // Get all recipes
    Route::post('/recipes', [RecipeController::class, 'store']);                   // Create new recipe
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);            // Get single recipe
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update']);          // Update recipe
    Route::patch('/recipes/{recipe}', [RecipeController::class, 'update']);        // Update recipe (PATCH)
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']);      // Delete recipe

    // Custom recipe routes (authenticated users only)
    Route::get('/my-recipes', [RecipeController::class, 'myRecipes']);

    // User Profile routes (authenticated users only)
    Route::prefix('user')->group(function () {
        Route::get('profile', [UserProfileController::class, 'show']);
        Route::put('profile', [UserProfileController::class, 'update']);
        Route::post('profile/avatar', [UserProfileController::class, 'uploadAvatar']);
        Route::delete('profile/avatar', [UserProfileController::class, 'removeAvatar']);
        Route::post('change-password', [UserProfileController::class, 'changePassword']);
    });

    // Social Features routes (authenticated users only)
    Route::prefix('social')->group(function () {
        Route::get('/feed', [SocialController::class, 'getFeed']);                     // Authenticated feed (with like status)
        Route::post('/recipes/{recipe}/like', [SocialController::class, 'toggleLike']); // Like/Unlike recipe
        Route::get('/recipes/{recipe}/stats', [SocialController::class, 'getRecipeStats']); // Get recipe stats
    });
});
