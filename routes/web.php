<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AddOnRuleController;
use App\Http\Controllers\BikeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\DistanceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PackageTypeController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkloadController;
use App\Http\Controllers\PostalCodeController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/upload-csv', function () {
    return view('upload_csv');
})->name('uploadCSV');

Route::post('/parse-csv', [App\Http\Controllers\HomeController::class, 'parseCSV'])->name('parseCSV');
Route::get('/',  'App\Http\Controllers\UserController@index')->name('users.index')->middleware('auth');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/get-client-info/{clientId}', [ClientController::class, 'getClientInfo'])
    ->name('getClientInfo')->middleware('auth');
Auth::routes();
Route::group(['prefix' => 'users'], function(){
    Route::get('',                          [UserController::class, 'index'])->name('user.index')->middleware('auth');
    Route::get('create',                    [UserController::class, 'create'])->name('user.create')->middleware('auth');
    Route::post('store',                    [UserController::class, 'store'])->name('user.store')->middleware('auth');
    Route::get('edit/{user}',               [UserController::class, 'edit'])->name('user.edit')->middleware('auth');
    Route::post('update/{user}',            [UserController::class, 'update'])->name('user.update')->middleware('auth');
    Route::get('delete/{user}',             [UserController::class, 'delete'])->name('user.delete')->middleware('auth');
    Route::post('destroy/{user}',           [UserController::class, 'destroy'])->name('user.destroy')->middleware('auth');
    Route::get('show/{user}',               [UserController::class, 'show'])->name('user.show')->middleware('auth');
    Route::get('getCouriersWithWorkloadOnDay/{date?}', [UserController::class, 'getCouriersWithWorkloadOnDay'])->name('user.getCouriersWithWorkloadOnDay')->middleware('auth');
    Route::get('workload/{user}/{month?}/{year?}', [UserController::class, 'workload'])
    ->where(['year' => '\d{4}', 'month' => '\d{1,2}'])
    ->name('user.workload')
    ->middleware('auth');
    Route::post('updateRole/{user}',    [UserController::class, 'updateRole'])->name('user.updateRole')->middleware('auth');

});
Route::group(['prefix' => 'roles'], function(){
    Route::get('',                          [RoleController::class, 'index'])->name('role.index')->middleware('auth'); 
});
Route::group(['prefix' => 'clients'], function(){
    Route::get('',                  [ClientController::class, 'index'])->name('client.index')->middleware('auth');
    Route::get('create',            [ClientController::class, 'create'])->name('client.create')->middleware('auth');
    Route::post('store',            [ClientController::class, 'store'])->name('client.store')->middleware('auth');
    //Route::get('edit/{client}',     [ClientController::class, 'edit'])->name('client.edit')->middleware('auth');
    Route::post('update',           [ClientController::class, 'update'])->name('client.update')->middleware('auth');
    Route::post('delete',   [ClientController::class, 'destroy'])->name('client.delete')->middleware('auth');
    Route::delete('destroy',  [ClientController::class, 'destroy'])->name('client.destroy')->middleware('auth');
    Route::get('show/{client}',     [ClientController::class, 'show'])->name('client.show')->middleware('auth');
    Route::post('createBackup', [ClientController::class, 'createBackup'])->name('client.createBackup')->middleware('auth'); 
    Route::get('searchClients',    [ClientController::class, 'searchClients'])->name('client.searchClients')->middleware('auth');
    Route::get('fetchClientsPaginate', [ClientController::class, 'fetchClientsPaginate'])->name('client.fetch')->middleware('auth');
});
Route::group(['prefix' => 'jobs'], function(){
    Route::get('',                  [JobController::class, 'index'])->name('job.index')->middleware('auth');
    Route::get('create/{customjob?}',            [JobController::class, 'create'])->name('job.create')->middleware('auth');
    Route::post('store',            [JobController::class, 'store'])->name('job.store')->middleware('auth');
    Route::get('edit/{job}',        [JobController::class, 'edit'])->name('job.edit')->middleware('auth');
    Route::post('update',      [JobController::class, 'update'])->name('job.update')->middleware('auth');
    Route::post('updateStatus/{job}',[JobController::class, 'updateStatus'])->name('job.updateStatus')->middleware('auth');
    Route::post('delete',   [JobController::class, 'destroy'])->name('job.delete')->middleware('auth');
    // Route::delete('destroy/{job}',  [JobController::class, 'destroy'])->name('job.destroy')->middleware('auth');
    Route::get('show/{job}',        [JobController::class, 'show'])->name('job.show')->middleware('auth');
    Route::get('assign',            [JobController::class, 'assign'])->name('job.assign')->middleware('auth');
    Route::post('update-job-ajax',      [JobController::class, 'updateJobAjax'])->name('job.updateajax')->middleware('auth');
    Route::get('getJobInfo/{id}',     [JobController::class, 'getJobInfo'])->name('job.getJobInfo')->middleware('auth');
    Route::get('fetchJobsPaginate', [JobController::class, 'fetchJobsPaginate'])->name('job.fetch')->middleware('auth');
});
Route::group(['prefix' => 'days'], function(){
    Route::get('',                  [DayController::class, 'index'])->name('day.index')->middleware('auth');
    // Route::get('create',            [JobController::class, 'create'])->name('job.create')->middleware('auth');
    // Route::post('store',            [JobController::class, 'store'])->name('job.store')->middleware('auth');
    // Route::get('edit/{job}',        [JobController::class, 'edit'])->name('job.edit')->middleware('auth');
    // Route::put('update/{job}',      [JobController::class, 'update'])->name('job.update')->middleware('auth');
    // Route::post('updateStatus/{job}',[JobController::class, 'updateStatus'])->name('job.updateStatus')->middleware('auth');
    // Route::get('delete/{job}',   [JobController::class, 'delete'])->name('job.delete')->middleware('auth');
    // Route::delete('destroy/{job}',  [JobController::class, 'destroy'])->name('job.destroy')->middleware('auth');
    Route::get('show/{day}',        [DayController::class, 'show'])->name('day.show')->middleware('auth');
    Route::get('showdashboard/{date}',        [DayController::class, 'showdashboard'])->name('day.showdashboard')->middleware('auth');
    // Route::get('assign',            [JobController::class, 'assign'])->name('job.assign')->middleware('auth');
    Route::get('/get-free-bikes', [DayController::class, 'getFreeBikes'])->name('day.getFreeBikes')->middleware('auth');
    Route::get('/get-free-couriers', [DayController::class, 'getFreeCouriers'])->name('day.getFreeCouriers')->middleware('auth');
});
Route::group(['prefix' => 'workloads'], function(){
    Route::get('',                  [WorkloadController::class, 'index'])->name('workload.index')->middleware('auth');
    // Route::get('create',            [JobController::class, 'create'])->name('job.create')->middleware('auth');
    Route::post('store',            [WorkloadController::class, 'store'])->name('workload.store')->middleware('auth');
    Route::post('/storeJavascript',  [WorkloadController::class, 'storeJavascript'])->name('workload.storeJavascript')->middleware('auth');
    // Route::get('edit/{job}',        [JobController::class, 'edit'])->name('job.edit')->middleware('auth');
    // Route::put('update/{job}',      [JobController::class, 'update'])->name('job.update')->middleware('auth');
    Route::post('/updateJavascript',  [WorkloadController::class, 'updateJavascript'])->name('workload.updateJavascript')->middleware('auth');
    // Route::get('delete/{job}',   [JobController::class, 'delete'])->name('job.delete')->middleware('auth');
    Route::post('/deleteJavascript',  [WorkloadController::class, 'deleteJavascript'])->name('workload.deleteJavascript')->middleware('auth');
    // Route::delete('destroy/{job}',  [JobController::class, 'destroy'])->name('job.destroy')->middleware('auth');
    //Route::get('show/{day}',        [DayController::class, 'show'])->name('day.show')->middleware('auth');
    // Route::get('assign',            [JobController::class, 'assign'])->name('job.assign')->middleware('auth');
    Route::get('calendar/{month?}/{year?}', [WorkloadController::class, 'calendar'])
    ->where(['year' => '\d{4}', 'month' => '\d{1,2}'])
    ->name('workload.calendar')
    ->middleware('auth');
});
Route::group(['prefix' => 'addonrules'], function(){
    Route::get('',                  [AddOnRuleController::class, 'index'])->name('addonrule.index')->middleware('auth');
    Route::post('store',            [AddOnRuleController::class, 'store'])->name('addonrule.store')->middleware('auth');
    Route::post('update',   [AddOnRuleController::class, 'update'])->name('addonrule.update')->middleware('auth');
    Route::post('delete',   [AddOnRuleController::class, 'destroy'])->name('addonrule.delete')->middleware('auth');
    Route::post('createBackup',     [AddOnRuleController::class, 'createBackup'])->name('addonrule.createBackup')->middleware('auth');
    Route::get('getAddOnRuleInfo/{id}',     [AddOnRuleController::class, 'getAddOnRuleInfo'])->name('addonrule.getAddOnRuleInfo')->middleware('auth');
    Route::get('getRulesForDate/{date}',     [AddOnRuleController::class, 'getRulesForDate'])->name('addonrule.getRulesForDate')->middleware('auth');
    Route::get('getRulesForDateAndClient/{date}/{client}',     [AddOnRuleController::class, 'getRulesForDateAndClient'])->name('addonrule.getRulesForDateAndClient')->middleware('auth');
    Route::get('getDistancePriceForDateAndClient/{date}/{client}',     [AddOnRuleController::class, 'getDistancePriceForDateAndClient'])->name('addonrule.getDistancePriceForDateAndClient')->middleware('auth');
    Route::get('getPriceForDistance/{date}/{client}/{distance}',     [AddOnRuleController::class, 'getPriceForDistance'])->name('addonrule.getPriceForDistance')->middleware('auth');
});
Route::group(['prefix' => 'distances'], function(){
    //Route::get('',                  [AddOnRuleController::class, 'index'])->name('addonrule.index')->middleware('auth');
    //Route::get('create',            [AddOnRuleController::class, 'create'])->name('addonrule.create')->middleware('auth');
    //Route::post('store',            [AddOnRuleController::class, 'store'])->name('addonrule.store')->middleware('auth');
    //Route::get('edit/{client}',     [ClientController::class, 'edit'])->name('client.edit')->middleware('auth');
    //Route::put('update/{client}',   [ClientController::class, 'update'])->name('client.update')->middleware('auth');
    //Route::get('delete/{client}',   [ClientController::class, 'delete'])->name('client.delete')->middleware('auth');
    //Route::delete('destroy/{client}',  [ClientController::class, 'destroy'])->name('client.destroy')->middleware('auth');
    //Route::get('show/{client}',     [ClientController::class, 'show'])->name('client.show')->middleware('auth');
    //Route::get('findAddOnRule',     [AddOnRuleController::class, 'findAddOnRule'])->name('addonrule.findAddOnRule')->middleware('auth');
    //Route::post('createBackup', [ClientController::class, 'createBackup'])->name('client.createBackup')->middleware('auth'); 
    Route::get('getDistance', [DistanceController::class, 'getDistance'])->name('distance.getDistance')->middleware('auth'); 
});
Route::group(['prefix'  =>  'bikes'],function(){
    Route::get('',                  [BikeController::class, 'index'])->name('bike.index')->middleware('auth');
    Route::post('update',           [BikeController::class, 'update'])->name('bike.update')->middleware('auth');
    Route::post('delete',           [BikeController::class, 'destroy'])->name('bike.delete')->middleware('auth');
    Route::post('store',            [BikeController::class, 'store'])->name('bike.store')->middleware('auth');
});
Route::group(['prefix'  => 'statuses'],function(){
    Route::get('/',                 [StatusController::class, 'index'])->name('status.index')->middleware('auth');
    Route::post('update',           [StatusController::class, 'update'])->name('status.update')->middleware('auth');
    Route::post('delete',           [StatusController::class, 'destroy'])->name('status.delete')->middleware('auth');
    Route::post('store',            [StatusController::class, 'store'])->name('status.store')->middleware('auth');
    Route::post('createBackup',     [StatusController::class, 'createBackup'])->name('status.createBackup')->middleware('auth');
    Route::get('getStatusInfo/{id}',     [StatusController::class, 'getStatusInfo'])->name('status.getStatusInfo')->middleware('auth');
});
Route::group(['prefix'  => 'packageTypes'],function(){
    Route::get('',                  [PackageTypeController::class, 'index'])->name('packageType.index')->middleware('auth');
    Route::post('update',           [PackageTypeController::class, 'update'])->name('packageType.update')->middleware('auth');
    Route::post('delete',           [PackageTypeController::class, 'destroy'])->name('packageType.delete')->middleware('auth');
    Route::post('store',            [PackageTypeController::class, 'store'])->name('packageType.store')->middleware('auth');
    Route::post('createBackup',     [PackageTypeController::class, 'createBackup'])->name('packageType.createBackup')->middleware('auth');
    Route::get('getPackageTypeInfo/{id}',     [PackageTypeController::class, 'getPackageTypeInfo'])->name('packageType.getPackageTypeInfo')->middleware('auth');
});
Route::group(['prefix'  => 'tasks'],function(){
    Route::get('',                  [TaskController::class, 'index'])->name('task.index')->middleware('auth');
    Route::post('update',           [TaskController::class, 'update'])->name('task.update')->middleware('auth');
    Route::post('delete',           [TaskController::class, 'destroy'])->name('task.delete')->middleware('auth');
    Route::post('store',            [TaskController::class, 'store'])->name('task.store')->middleware('auth');
    Route::post('createBackup',     [TaskController::class, 'createBackup'])->name('task.createBackup')->middleware('auth');
    Route::get('getTaskInfo/{id}',  [TaskController::class, 'getTaskInfo'])->name('task.getTaskInfo')->middleware('auth');
});
Route::group(['prefix'  => 'postalCode'],function(){
    Route::get('',                  [PostalCodeController::class, 'index'])->name('postalCode.index')->middleware('auth');
    // Route::post('update',           [PostalCodeController::class, 'update'])->name('task.update')->middleware('auth');
    // Route::post('delete',           [PostalCodeController::class, 'destroy'])->name('task.delete')->middleware('auth');
    // Route::post('store',            [PostalCodeController::class, 'store'])->name('task.store')->middleware('auth');
    // Route::post('createBackup',     [PostalCodeController::class, 'createBackup'])->name('task.createBackup')->middleware('auth');
    // Route::get('getTaskInfo/{id}',  [PostalCodeController::class, 'getTaskInfo'])->name('task.getTaskInfo')->middleware('auth');
});
