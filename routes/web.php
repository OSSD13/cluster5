<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseTestController;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/register', [DatabaseTestController::class, 'create']);
Route::get('/displayLogin', [DatabaseTestController::class, 'displayLogin']);
