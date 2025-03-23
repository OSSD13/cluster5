<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\AnotherController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BranchReportController;

// Default welcome page
// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\GoogleAuthController;
use App\Http\Middleware\CheckLogin;

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google-auth');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

Route::get('/login', function () {
    return view('auth.login');
}); // แก้ชื่อ method ให้ตรง (Login → login)

Route::post('/login', action: [LoginController::class, 'login']);


// testing
Route::middleware([CheckLogin::class])->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    });
    Route::get('/map', function () {
        return view('map.index');
    });
    Route::get('/branch', function () {
        return view('branch.index');
    });
    Route::get('/poi', function () {
        return view('poi.index');
    });
    Route::get('/user', function () {
        return view('user.index');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // เปลี่ยนเป็น POST และเพิ่ม name


    // APIs
    Route::get('/api/getSubordinate', [BranchReportController::class, 'getSubordinate']);
    Route::get('/api/getBranchReport', [BranchReportController::class, 'getBranchReport']);
<<<<<<< HEAD
=======

>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
    // Database test routes
    // Route::get('/register', [DatabaseTestController::class, 'createUser']);
    Route::get('/displayLogin', [DatabaseTestController::class, 'displayLogin']);
    Route::get('/displaySub', [DatabaseTestController::class, 'displaySub']);
    Route::get('/displayBs', [DatabaseTestController::class, 'displayBs']);

    // Google Maps URL conversion routes
    Route::get('/convert-link', [AnotherController::class, 'showForm']);
    Route::post('/convert-url', [AnotherController::class, 'handleConversion'])->name('handleConversion');

    
});

<<<<<<< HEAD
Route::get('/test', function () {
=======

Route::get('/jeng', function() {
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> 80e8106 (fix(routes): update view path for jeng route)
    return view('ok');
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
>>>>>>> d77ac94 (Add new component layout and route for test view)
<<<<<<< HEAD
>>>>>>> 3d7c218 (Add new component layout and route for test view)
});

Route::get('/wacha', function() {
>>>>>>> ac93458 (Add new component layout and route for test view)
    return view('test');
<<<<<<< HEAD
=======
>>>>>>> 883039d (Chg)
=======
=======
    return view('/test/ok');
>>>>>>> fa0476f (fix(routes): update view path for jeng route)
=======
>>>>>>> 1478690 (Add new component layout and route for test view)
=======
>>>>>>> ed31f7f (Chg)
=======
=======
    return view('/test/ok');
>>>>>>> 4c99e81 (fix(routes): update view path for jeng route)
>>>>>>> 80e8106 (fix(routes): update view path for jeng route)
});

Route::get('/wacha', function() {
    return view('test');
<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>> fe56918 (Add new component layout and route for test view)
=======
    return view('ok');
>>>>>>> 8e2818b (Chg)
=======
>>>>>>> 1478690 (Add new component layout and route for test view)
=======
=======
>>>>>>> 30d3bf4 (Chg)
<<<<<<< HEAD
>>>>>>> ed31f7f (Chg)
=======
=======
>>>>>>> d77ac94 (Add new component layout and route for test view)
>>>>>>> 3d7c218 (Add new component layout and route for test view)
});