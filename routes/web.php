<?php

use Illuminate\Support\Facades\Route;

// SPA Route - Catch all routes and serve Vue app
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
