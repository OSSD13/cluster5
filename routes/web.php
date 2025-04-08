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
use App\Http\Middleware\CheckLogin;
use App\Http\Controllers\InsertPointOfInterestTypeController;

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google-auth');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login'); // แก้ชื่อ method ให้ตรง (Login → login)

Route::post('/login', [LoginController::class, 'login'])->name('login'); // เปลี่ยนเป็น POST และเพิ่ม name



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
    Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
    Route::get('/branch/manage', [BranchController::class, 'manage'])->name('branch.manage.index');

    Route::get('/poi', function () { return view('poi.index'); });
    Route::get('/poi/create', [PointOfInterestController::class, 'createPage'])->name('poi.create');
    Route::get('/poi/edit', [PointOfInterestController::class, 'editPage'])->name('poi.edit');
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
    Route::get('/api/getSubordinate', [BranchReportController::class, 'getSubordinate'])->name('api.report.getSubordinate');
    Route::get('/api/getBranchReport', [BranchReportController::class, 'getBranchReport'])->name('api.report.getBranchReport');
    // /api/getRegionBranch
    Route::get('/api/getRegionBranch', [BranchReportController::class, 'getRegionBranch'])->name('api.report.getRegionBranch');

    Route::get('/api/poi', [PointOfInterestController::class, 'queryPoi'])->name('api.poi.query');
    Route::post('/api/poi/edit', [PointOfInterestController::class, 'editPoi'])->name('api.poi.edit');
    Route::post('/poi/insert', [PointOfInterestController::class, 'insert'])->name('poi.insert');
    Route::post('/poi/type/insert', [InsertPointOfInterestTypeController::class, 'insert'])->name('poi.type.insert');
    Route::post('/api/poi/create', [PointOfInterestController::class, 'createPoi'])->name('api.poi.create');

    Route::get('/api/poit/query', [PointOfInterestTypeController::class, 'queryPoit'])->name('api.poit.query');
    Route::get('/api/poit/query/all', [PointOfInterestTypeController::class, 'allPoit'])->name('api.poit.query.all');
    Route::get('/api/poit', [PointOfInterestTypeController::class, 'getPoit'])->name('api.poit.get');
    Route::post('/api/poit/create', [PointOfInterestTypeController::class, 'createPoit'])->name('api.poit.create');
    Route::post('/poi/type/insert', [PointOfInterestTypeController::class, 'insert'])->name('poi.type.insert');
    Route::post('/api/poit/delete', [PointOfInterestTypeController::class, 'deletePoit'])->name('api.poit.delete');
    Route::post('/api/poit/edit', [PointOfInterestTypeController::class, 'editPoit'])->name('api.poit.edit');

    Route::get('/api/locations', [LocationController::class, 'getLocations'])->name('api.locations');

    Route::get('/api/branch/query', [BranchController::class, 'queryBranch'])->name('api.branch.query');
    Route::post('/api/branch/create', [BranchController::class, 'createBranch'])->name('api.branch.create');
    Route::post('/api/branch/edit', [BranchController::class, 'editBranch'])->name('api.branch.edit');
    Route::post('/api/branch/delete', [BranchController::class, 'deleteBranch'])->name('api.branch.delete');
    Route::get('/api/branch', [BranchController::class, 'getBranch'])->name('api.branch.get');

    Route::get('/api/user/query', [UserController::class, 'queryUser'])->name('api.user.query');
    Route::get('/api/user/query/all', [UserController::class, 'queryAllUser'])->name('api.user.query.all');
    Route::get('/api/user', [UserController::class, 'getUser'])->name('api.user.get');
    Route::post('/api/user/create', [UserController::class, 'createUser'])->name('api.user.create');
    Route::post('/api/user/edit', [UserController::class, 'editUser'])->name('api.user.edit');
    Route::post('/api/user/delete', [UserController::class, 'deleteUser'])->name('api.user.delete');



    // Test routes
    Route::get('/api/map', [MapController::class, 'getNearbyPOIsGroupedByType'])->name('api.map.get');



    Route::get('/displayLogin', [DatabaseTestController::class, 'displayLogin']);
    Route::get('/displaySub', [DatabaseTestController::class, 'displaySub']);
    Route::get('/displayBs', [DatabaseTestController::class, 'displayBs']);

    // Google Maps URL conversion routes
    Route::get('/convert-link', [GoogleMapController::class, 'showForm']);
    Route::post('/convert-url', [GoogleMapController::class, 'convertShareToLatLng'])->name('handleConversion');


});


Route::get('/test', function () {
    return view('test');
});
