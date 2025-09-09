<?php

use Illuminate\Support\Facades\Route;

// API routes are in routes/api.php (Laravel default)
require __DIR__.'/api.php';

// Catch-all route for Vue SPA
Route::get('/login', function () {
    return view('welcome'); // let Vue handle the login page
})->name('login');
