<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CollectionController;

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

// Public comment routes (anyone can view comments)
Route::get('/recipes/{recipe}/comments', [CommentController::class, 'index']); // Get all comments for a recipe

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
        Route::post('/recipes/{recipe}/like', [SocialController::class, 'toggleLike']); // Like/Unlike recipe
        Route::get('/recipes/{recipe}/stats', [SocialController::class, 'getRecipeStats']); // Get recipe stats
    });

    // Comment routes (authenticated users only)
    Route::prefix('recipes/{recipe}/comments')->group(function () {
        Route::post('/', [CommentController::class, 'store']);                   // Add a comment
        Route::put('/{comment}', [CommentController::class, 'update']);          // Update a comment
        Route::delete('/{comment}', [CommentController::class, 'destroy']);      // Delete a comment
    });

    // Collection routes (authenticated users only)
    Route::prefix('collections')->group(function () {
        Route::get('/', [CollectionController::class, 'index']);                 // Get user's collections
        Route::post('/', [CollectionController::class, 'store']);                // Create new collection
        Route::get('/{collection}', [CollectionController::class, 'show']);      // Get specific collection
        Route::put('/{collection}', [CollectionController::class, 'update']);    // Update collection
        Route::delete('/{collection}', [CollectionController::class, 'destroy']); // Delete collection
        Route::post('/{collection}/recipes', [CollectionController::class, 'addRecipe']); // Add recipe to collection
        Route::delete('/{collection}/recipes', [CollectionController::class, 'removeRecipe']); // Remove recipe from collection
    });

});

// Public collections route (no auth required)
Route::get('/public/collections', [CollectionController::class, 'publicCollections']); // Get public collections
