<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\AiRecipeController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\StoreProductController;
use App\Http\Controllers\Api\OrderController;

// =============================================================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================================================

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API working!']);
});

// AI test route (for development)
Route::get('/ai/test', function () {
    return response()->json([
        'message' => 'AI integration ready!',
        'huggingface_key' => config('services.huggingface.api_key') ? 'Configured' : 'Not configured',
        'note' => 'Add your Hugging Face API key to .env file'
    ]);
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [OtpController::class, 'verify']);
Route::post('/resend-otp', [OtpController::class, 'resend']);

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

    // AI Recipe Generation routes (authenticated users only)
    Route::prefix('ai')->group(function () {
        Route::post('/generate-recipe', [AiRecipeController::class, 'generateRecipe']); // Generate AI recipe
        Route::get('/generated-recipes', [AiRecipeController::class, 'getAiGeneratedRecipes']); // Get AI generated recipes
    });

    // Store routes (authenticated users only)
    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'index']); // Get nearby stores
        Route::post('/', [StoreController::class, 'store']); // Create store
        Route::get('/my-store', [StoreController::class, 'myStore']); // Get user's store
        Route::get('/{store}', [StoreController::class, 'show']); // Get specific store
        Route::put('/{store}', [StoreController::class, 'update']); // Update store
        Route::get('/{store}/orders', [StoreController::class, 'orders']); // Get store orders
    });

    // Store Product routes (authenticated users only)
    Route::prefix('store-products')->group(function () {
        Route::get('/', [StoreProductController::class, 'index']); // Get store products
        Route::post('/', [StoreProductController::class, 'store']); // Add product to store
        Route::get('/my-products', [StoreProductController::class, 'myProducts']); // Get user's store products
        Route::get('/{product}', [StoreProductController::class, 'show']); // Get specific product
        Route::put('/{product}', [StoreProductController::class, 'update']); // Update product
        Route::delete('/{product}', [StoreProductController::class, 'destroy']); // Remove product
    });

    // Order routes (authenticated users only)
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']); // Get user's orders
        Route::post('/', [OrderController::class, 'store']); // Place order
        Route::get('/{order}', [OrderController::class, 'show']); // Get specific order
        Route::put('/{order}', [OrderController::class, 'update']); // Update order status
        Route::post('/{order}/cancel', [OrderController::class, 'cancel']); // Cancel order
    });

});

// Public collections route (no auth required)
Route::get('/public/collections', [CollectionController::class, 'publicCollections']); // Get public collections
