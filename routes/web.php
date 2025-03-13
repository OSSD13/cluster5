<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\AnotherController;
use App\Http\Controllers\LoginController;

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// Database test routes
Route::get('/register', [DatabaseTestController::class, 'createUser']);
Route::get('/displayLogin', [DatabaseTestController::class, 'displayLogin']);
Route::get('/displaySub', [DatabaseTestController::class, 'displaySub']);
Route::get('/displayBs', [DatabaseTestController::class, 'displayBs']);

// Google Maps URL conversion routes
Route::get('/convert-link', [AnotherController::class, 'showForm']);
Route::post('/convert-url', [AnotherController::class, 'handleConversion'])->name('handleConversion');

use App\Http\Controllers\GoogleAuthController;

Route::get('/displayTestLogin', [DatabaseTestController::class, 'displayTestLogin']);
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google-auth');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);
Route::get('/login', [LoginController::class, 'login']);
