<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
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

Auth::routes();
Route::group(['prefix' => 'users'], function(){
    Route::get('',                  [UserController::class, 'index'])->name('user.index')->middleware('auth');
    Route::get('create',            [UserController::class, 'create'])->name('user.create')->middleware('auth');
    Route::post('store',            [UserController::class, 'store'])->name('user.store')->middleware('auth');
    Route::get('edit/{user}',       [UserController::class, 'edit'])->name('user.edit')->middleware('auth');
    Route::post('update/{user}',    [UserController::class, 'update'])->name('user.update')->middleware('auth');
    Route::get('delete/{user}',     [UserController::class, 'destroy'])->name('user.destroy')->middleware('auth');
    Route::get('show/{user}',       [UserController::class, 'show'])->name('user.show')->middleware('auth');
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
