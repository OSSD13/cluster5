<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/register', [RegisterController::class, 'create']);
Route::get('/displayLogin', [RegisterController::class, 'displayLogin']);
