<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\AnotherController;
use App\Http\Controllers\LoginController;

// Default welcome page
// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\GoogleAuthController;
use App\Http\Middleware\CheckLogin;

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google-auth');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

Route::get('/login', action: [LoginController::class, 'index']);
Route::post('/login', function () {
    return view('auth.login');
}); // แก้ชื่อ method ให้ตรง (Login → login)


// testing
Route::middleware([CheckLogin::class])->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    });
    Route::get('/map', function () {
        return view('dashboard.index');
    });
    Route::get('/branch', function () {
        return view('dashboard.index');
    });
    Route::get('/poi', function () {
        return view('dashboard.index');
    });
    Route::get('/user', function () {
        return view('dashboard.index');
    });



    Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // เปลี่ยนเป็น POST และเพิ่ม name

    // Database test routes
    // Route::get('/register', [DatabaseTestController::class, 'createUser']);
    Route::get('/displayLogin', [DatabaseTestController::class, 'displayLogin']);
    Route::get('/displaySub', [DatabaseTestController::class, 'displaySub']);
    Route::get('/displayBs', [DatabaseTestController::class, 'displayBs']);

    // Google Maps URL conversion routes
    Route::get('/convert-link', [AnotherController::class, 'showForm']);
    Route::post('/convert-url', [AnotherController::class, 'handleConversion'])->name('handleConversion');
});


Route::get('/jeng', function() {
    return view('ok');
});

Route::get('/wacha', function() {
    return view('test');
});