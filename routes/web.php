<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseTestController;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/register', [DatabaseTestController::class, 'createUser']);
Route::get('/displayLogin', [DatabaseTestController::class, 'displayLogin']);
Route::get('/displaySub', [DatabaseTestController::class, 'displaySub']);
Route::get('/displayBs', [DatabaseTestController::class, 'displayBs']);
