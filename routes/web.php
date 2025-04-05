<?php

use App\Http\Controllers\PointOfInterestController;
use App\Http\Controllers\PointOfInterestTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\AnotherController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BranchReportController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Middleware\CheckLogin;

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google-auth');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

Route::get('/login', function () {
    return view('auth.login');
}); // แก้ชื่อ method ให้ตรง (Login → login)

Route::post('/login',[LoginController::class, 'login']);



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
    Route::get('/poi/create', [PointOfInterestController::class, 'create'])->name('poi.create');
    Route::post('/poi/insert', [PointOfInterestController::class, 'insert'])->name('poi.insert');
    Route::get('/poi/edit', [PointOfInterestController::class, 'edit'])->name('poi.edit');
    Route::delete('/poi/{id}', [PointOfInterestController::class, 'destroy'])->name('poi.destroy');
    Route::get('/poi/type/create', [PointOfInterestTypeController::class, 'create'])->name('poi.type.create');
    Route::post('/poi/type/insert', [PointOfInterestTypeController::class, 'insert'])->name('poi.type.insert');
    Route::get('/poi/type/edit', [PointOfInterestTypeController::class, 'edit'])->name('poi.type.edit');
    Route::get('/poi/type', [PointOfInterestTypeController::class, 'index'])->name('poi.type.index');
    Route::get('/poi/', [PointOfInterestController::class, 'index'])->name('poi.index');


    Route::get('/user', function () {
        return view('user.index');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // เปลี่ยนเป็น POST และเพิ่ม name


    // APIs
    Route::get('/api/getSubordinate', [BranchReportController::class, 'getSubordinate']);
    Route::get('/api/getBranchReport', [BranchReportController::class, 'getBranchReport']);

    // /api/getRegionBranch
    Route::get('/api/getRegionBranch', [BranchReportController::class, 'getRegionBranch']);




    Route::get('/displayLogin', [DatabaseTestController::class, 'displayLogin']);
    Route::get('/displaySub', [DatabaseTestController::class, 'displaySub']);
    Route::get('/displayBs', [DatabaseTestController::class, 'displayBs']);

    // Google Maps URL conversion routes
    Route::get('/convert-link', [AnotherController::class, 'showForm']);
    Route::post('/convert-url', [AnotherController::class, 'handleConversion'])->name('handleConversion');


});


Route::get('/test', function () {
    return view('test');
});