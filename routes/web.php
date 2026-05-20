<?php

use App\Events\LinkPhone;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MasterUserController;
use App\Http\Controllers\ManageMastersController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BoatController;
use App\Http\Controllers\UserBoatController;
use App\Http\Controllers\TripsController;
use App\Http\Controllers\BoatMapController;
use App\Http\Controllers\FleetMapController;
use App\Http\Controllers\BoatStatsController;
use App\Http\Controllers\BoatRawDataController;
use App\Http\Controllers\BoatInsureController;
use App\Http\Controllers\BoatTripController;
use App\Http\Controllers\TripDetectionConfigController;
use Google\Service\AIPlatformNotebooks\Event;
use Google\Service\AlertCenter\UserChanges;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ForgotPasswordController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware(['auth'])->group(function () {
    Route::get('/trips', [BoatTripController::class, 'index'])->name('trips.index');
    Route::get('/trips/{trip}', [BoatTripController::class, 'show'])->name('trips.show');
    Route::get('/trips/{trip}/edit', [BoatTripController::class, 'edit'])->name('trips.edit');
    Route::put('/trips/{trip}', [BoatTripController::class, 'update'])->name('trips.update');

    Route::post('/trips/{trip}/confirm', [BoatTripController::class, 'confirm'])->name('trips.confirm');
    Route::post('/trips/{trip}/ignore', [BoatTripController::class, 'ignore'])->name('trips.ignore');
    Route::post('/trips/{trip}/merge-next', [BoatTripController::class, 'mergeNext'])->name('trips.merge-next');
    Route::delete('/trips/{trip}', [BoatTripController::class, 'destroy'])->name('trips.destroy');
	Route::get('/trips/{trip}/boundary-data', [BoatTripController::class, 'boundaryData'])->name('trips.boundary-data');
	Route::post('/trips/{trip}/save-boundary', [BoatTripController::class, 'saveBoundary'])->name('trips.save-boundary');
	Route::get('/trips/{trip}/nudge-record', [BoatTripController::class, 'nudgeRecord'])->name('trips.nudge-record');
    Route::get('/trip-settings', [TripDetectionConfigController::class, 'edit'])->name('trip-settings.edit');
    Route::put('/trip-settings', [TripDetectionConfigController::class, 'update'])->name('trip-settings.update');
});


Route::get('/boat-stats/{mac}', [BoatStatsController::class, 'index']) ->name('boat.stats');

Route::get('/', [HomeController::class, 'searchPage'])->name('search');
Route::post('/search', [HomeController::class, 'searchMac'])->name('search.mac');
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
Route::post('/assets/ajax/get-track-data', [TripsController::class, 'getTrackData'])->name('admin.getTrackData');
Route::post('/assets/ajax/get-log-data', [TripsController::class, 'getLogData'])->name('admin.getLogData');
Route::post('/assets/ajax/get-table-data', [TripsController::class, 'getTableData'])->name('admin.getTableData');
Route::post('/assets/ajax/get-speed-data', [TripsController::class, 'fetchSpeed'])->name('admin.fetchSpeed');
Route::post('/assets/ajax/get-engine-data', [TripsController::class, 'fetchEngine'])->name('admin.fetchEngine');
Auth::routes();


Route::get('/boat-raw/{mac}/{range?}', [BoatRawDataController::class, 'show']) ->name('boat.raw');

Route::get('/boat-insure/{mac}', [BoatInsureController::class, 'show'])  ->name('boat.insure');

Route::get('/fleet-map/boats', [FleetMapController::class, 'boats'])->name('fleet.map.boats');
Route::get('/fleet-map/data/{mac}/{days?}', [FleetMapController::class, 'boatData'])->name('fleet.map.boatData');
Route::get('/fleet-map', [FleetMapController::class, 'index']) ->name('fleet.map');

Route::get('/boat-map/{mac}/{days?}', [BoatMapController::class, 'show'])
    ->name('boat.map');

Route::group(['middleware' => ['auth']], function () {
   
    //Admin routes
    Route::middleware(['auth', 'isAdmin'])->group(function () {
        Route::get('admin', [AdminController::class, 'dashboard'])->name('dashboard');
		Route::get('/admin/conn', [AdminController::class, 'conn'])->name('conn');
        Route::get('/admin/setting', [AdminController::class, 'setting'])->name('setting');
        Route::post('/admin/edit_setting', [AdminController::class, 'editSetting'])->name('settings.edit');
        Route::get('add_to_settings/{upload_id}', [AdminController::class, 'addToSettings'])->name('add_to_settings');
        //manage master users
        Route::resource('masters', ManageMastersController::class);
        Route::resource('boats', BoatController::class);
        Route::get('get_admins', [BoatController::class, 'allAdmins'])->name('get_admins');
        Route::get('my-trips', [TripsController::class, 'myTrips'])->name('admin.myTrips');
        Route::get('get_assigned_admin/{boat_id}', [BoatController::class, 'getAssignedAdmin'])->name('get_assigned_admin');
        Route::post('assign_boat', [BoatController::class, 'assignBoat'])->name('assign_boat');
        Route::get('all_user_boats', [UserBoatController::class, 'allUserBoats'])->name('admin.userboats');
        Route::get('delete_user_boat/{id}', [UserBoatController::class, 'delUserBoat'])->name('delete_user_boat');
        Route::post('update_boat', [BoatController::class, 'updateBoat'])->name('update_boat');

    });
    Route::post('add_boat_note', [BoatController::class, 'addBoatNote'])->name('add_boat_note');
    Route::post('edit_boat_note', [BoatController::class, 'editBoatNote'])->name('edit_boat_note');
    Route::post('add_boat_file', [BoatController::class, 'addBoatFile'])->name('add_boat_file');
    Route::post('edit_boat_file', [BoatController::class, 'editBoatFile'])->name('edit_boat_file');
    Route::get('view_boat/{boat_id}', [BoatController::class, 'viewBoat'])->name('view_boat');
    Route::get('edit_note', [BoatController::class, 'editNote'])->name('edit_note');
    Route::get('edit_file', [BoatController::class, 'editFile'])->name('edit_file');
    Route::get('delete_note/{boat_id}', [BoatController::class, 'deleteNote'])->name('delete_note');
    Route::get('delete_file/{boat_id}', [BoatController::class, 'deleteFile'])->name('delete_file');
    Route::get('delete_boat_data/{data_id}', [TripsController::class, 'deleteBoatData'])->name('delete_boat_data');
    Route::resource('users', ManageUserController::class);
    Route::get('load-calendar', [TripsController::class, 'loadCalendar'])->name('admin.loadCalendar');
    Route::get('get_users', [BoatController::class, 'allUsers'])->name('get_users');
    Route::get('get_assigned_user/{boat_id}', [BoatController::class, 'getAssignedUser'])->name('get_assigned_user');
    Route::post('assign_boat_to_user', [BoatController::class, 'assignBoatToUser'])->name('assign_boat_to_user');
    //Master user routes
    Route::middleware(['auth', 'isMasterUser'])->group(function () {
        Route::get('master', [MasterUserController::class, 'dashboard'])->name('master.dashboard');
		Route::get('/master/conn', [MasterUserController::class, 'masterconn'])->name('master.conn');	
        Route::get('/master/setting', [MasterUserController::class, 'setting'])->name('master.setting');
        Route::post('/master/edit_setting', [MasterUserController::class, 'editSetting'])->name('master.settings.edit');
        Route::get('edit_master_boat/{boat_id}', [BoatController::class, 'editMasterBoat'])->name('master_boats.edit');
        Route::get('master_boats', [BoatController::class, 'masterBoats'])->name('master_boats');
        Route::post('master_update_boat', [BoatController::class, 'masterUpdateBoat'])->name('master_update_boat');
        Route::get('master/all_user_boats', [UserBoatController::class, 'MasterAllUserBoats'])->name('master.userboats');
        Route::get('master/delete_user_boat/{id}', [UserBoatController::class, 'MasterDelUserBoat'])->name('master.delete_user_boat');
       Route::get('master/my-trips', [TripsController::class, 'masterTrips'])->name('master.myTrips');
    });

    //User routes
    Route::get('user', [UserController::class, 'dashboard'])->name('user.dashboard');
	Route::get('/user/conn', [UserController::class, 'conn'])->name('user.conn');	
    Route::get('/user/setting', [UserController::class, 'setting'])->name('user.setting');
    Route::post('/user/edit_setting', [UserController::class, 'editSetting'])->name('user.settings.edit');
    Route::get('user/boats', [UserBoatController::class, 'userBoats'])->name('user.userboats');
    Route::get('user/delete_boat/{id}', [UserBoatController::class, 'delBoat'])->name('user.delete_boat');
    Route::get('user/my-trips', [TripsController::class, 'userTrips'])->name('user.myTrips');
    
});

