<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\DayController;
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
Route::get('/',  'App\Http\Controllers\UserController@index')->name('users.index')->middleware('auth');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/get-client-info/{clientId}', [ClientController::class, 'getClientInfo'])
    ->name('getClientInfo')->middleware('auth');
Auth::routes();
Route::group(['prefix' => 'users'], function(){
    Route::get('',                  [UserController::class, 'index'])->name('user.index')->middleware('auth');
    Route::get('create',            [UserController::class, 'create'])->name('user.create')->middleware('auth');
    Route::post('store',            [UserController::class, 'store'])->name('user.store')->middleware('auth');
    Route::get('edit/{user}',       [UserController::class, 'edit'])->name('user.edit')->middleware('auth');
    Route::post('update/{user}',    [UserController::class, 'update'])->name('user.update')->middleware('auth');
    Route::get('delete/{user}',     [UserController::class, 'delete'])->name('user.delete')->middleware('auth');
    Route::post('destroy/{user}',   [UserController::class, 'destroy'])->name('user.destroy')->middleware('auth');
    Route::get('show/{user}',       [UserController::class, 'show'])->name('user.show')->middleware('auth');
});
Route::group(['prefix' => 'clients'], function(){
    Route::get('',                  [ClientController::class, 'index'])->name('client.index')->middleware('auth');
    Route::get('create',            [ClientController::class, 'create'])->name('client.create')->middleware('auth');
    Route::post('store',            [ClientController::class, 'store'])->name('client.store')->middleware('auth');
    Route::get('edit/{client}',     [ClientController::class, 'edit'])->name('client.edit')->middleware('auth');
    Route::put('update/{client}',   [ClientController::class, 'update'])->name('client.update')->middleware('auth');
    Route::get('delete/{client}',   [ClientController::class, 'delete'])->name('client.delete')->middleware('auth');
    Route::delete('destroy/{client}',  [ClientController::class, 'destroy'])->name('client.destroy')->middleware('auth');
    Route::get('show/{client}',     [ClientController::class, 'show'])->name('client.show')->middleware('auth');
    
});
Route::group(['prefix' => 'jobs'], function(){
    Route::get('',                  [JobController::class, 'index'])->name('job.index')->middleware('auth');
    Route::get('create',            [JobController::class, 'create'])->name('job.create')->middleware('auth');
    Route::post('store',            [JobController::class, 'store'])->name('job.store')->middleware('auth');
    Route::get('edit/{job}',        [JobController::class, 'edit'])->name('job.edit')->middleware('auth');
    Route::put('update/{job}',      [JobController::class, 'update'])->name('job.update')->middleware('auth');
    Route::post('updateStatus/{job}',[JobController::class, 'updateStatus'])->name('job.updateStatus')->middleware('auth');
    // Route::get('delete/{job}',   [JobController::class, 'delete'])->name('job.delete')->middleware('auth');
    // Route::delete('destroy/{job}',  [JobController::class, 'destroy'])->name('job.destroy')->middleware('auth');
    Route::get('show/{job}',        [JobController::class, 'show'])->name('job.show')->middleware('auth');
    Route::get('assign',            [JobController::class, 'assign'])->name('job.assign')->middleware('auth');
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
    // Route::get('assign',            [JobController::class, 'assign'])->name('job.assign')->middleware('auth');
});