<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\AnotherController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\XxxController;

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
Route::get('/login', [LoginController::class, 'login']);

// New routes named XXX
Route::get('/XXX', [XxxController::class, 'showXxxPage']);
Route::post('/XXX', [XxxController::class, 'handleXxxForm']);