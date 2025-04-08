<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PointOfInterestController;
use App\Http\Controllers\PointOfInterestTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\GoogleMapController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BranchReportController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\SalesController;
use App\Http\Middleware\CheckLogin;

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google-auth');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

Route::get('/login', function () {
    return view('auth.login');
})->name('loginGet'); // แก้ชื่อ method ให้ตรง (Login → login)

Route::post('/login', [LoginController::class, 'login'])->name('loginPost'); // เปลี่ยนเป็น POST และเพิ่ม name



// testing
Route::middleware([CheckLogin::class])->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');
    Route::get('/map', function () {
        return view('map.index');
    })->name('map');
    Route::get('/branch/create', [BranchController::class, 'create'])->name('branch.create');
    Route::get('/branch/edit', [BranchController::class, 'edit'])->name('branch.edit');
    Route::get('/branch/', [BranchController::class, 'index'])->name('branch.index');
    Route::get('/branch/manage', [BranchController::class, 'manage'])->name('branch.manage.index');

    Route::get('/poi', function () { return view('poi.index'); })->name('poi.index');
    Route::get('/poi/create', [PointOfInterestController::class, 'createPage'])->name('poi.create');
    Route::get('/poi/edit', [PointOfInterestController::class, 'editPage'])->name('poi.edit');

    Route::get('/poi/type/create', [PointOfInterestTypeController::class, 'create'])->name('poi.type.create');
    Route::get('/poi/type/edit', [PointOfInterestTypeController::class, 'edit'])->name('poi.type.edit');
    Route::get('/poi/type', [PointOfInterestTypeController::class, 'index'])->name('poi.type.index');

    Route::get('/branch/create', 'branchCreate')->name('branch.create');
    Route::get('/branch/edit', 'branchEdit')->name('branch.edit');
    Route::get('/branch/', 'branchIndex')->name('branch.index');
    Route::get('/branch/manage','branchManageIndex' )->name('branch.manage.index');

    Route::get('/poi', 'poiIndex')->name('poi.index');
    Route::get('/poi/create', 'poiCreate')->name('poi.create');
    Route::get('/poi/edit', 'poiEdit')->name('poi.edit');

    Route::get('/poi/type', 'poiTypeIndex')->name('poi.type.index');
    Route::get('/poi/type/create', 'poiTypeCreate')->name('poi.type.create');
    Route::get('/poi/type/edit', 'poiTypeEdit')->name('poi.type.edit');

});

Route::get('/test', function () {
    return view('test');
});