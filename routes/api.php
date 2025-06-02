<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\UserProfileController;

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API working!']);
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Recipe routes (Manual)
    Route::get('/recipes', [RecipeController::class, 'index']);                    // Get all recipes
    Route::post('/recipes', [RecipeController::class, 'store']);                   // Create new recipe
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);            // Get single recipe
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update']);          // Update recipe
    Route::patch('/recipes/{recipe}', [RecipeController::class, 'update']);        // Update recipe (PATCH)
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']);      // Delete recipe

    // Custom recipe routes
    Route::get('/my-recipes', [RecipeController::class, 'myRecipes']);

    // User Profile routes
    Route::prefix('user')->group(function () {
        Route::get('profile', [App\Http\Controllers\Api\UserProfileController::class, 'show']);
        Route::put('profile', [App\Http\Controllers\Api\UserProfileController::class, 'update']);
        Route::post('profile/avatar', [App\Http\Controllers\Api\UserProfileController::class, 'uploadAvatar']);
        Route::delete('profile/avatar', [App\Http\Controllers\Api\UserProfileController::class, 'removeAvatar']);
        Route::post('change-password', [App\Http\Controllers\Api\UserProfileController::class, 'changePassword']);
    });
});
