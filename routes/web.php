<?php

use Illuminate\Support\Facades\Route;

// API routes (default Laravel)
require __DIR__.'/api.php';

// Public routes handled by Vue
Route::view('/', 'welcome')->name('home');          // Home page (landing page)
Route::view('/login', 'welcome')->name('login');    // Login page
Route::view('/register', 'welcome')->name('register'); // Register page

// Catch-all route for Vue SPA
Route::view('/{any}', 'welcome')->where('any', '^(?!api).*$');