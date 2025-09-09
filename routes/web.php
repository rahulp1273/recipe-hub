<?php

use Illuminate\Support\Facades\Route;

// API routes are in routes/api.php (Laravel default)
require __DIR__.'/api.php';

// Named routes for Vue SPA pages (optional, but useful)
Route::get('/login', function () {
    return view('welcome'); // let Vue handle login page
})->name('login');

Route::get('/register', function () {
    return view('welcome'); // let Vue handle register page
})->name('register');

// Catch-all route for all other Vue SPA routes
Route::get('/{any}', function () {
    return view('welcome'); // let Vue handle all front-end routing
})->where('any', '^(?!api).*$');