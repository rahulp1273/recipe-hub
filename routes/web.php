<?php

use Illuminate\Support\Facades\Route;

// API routes are in routes/api.php (Laravel default)
require __DIR__.'/api.php';

// Catch-all route for Vue SPA
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
