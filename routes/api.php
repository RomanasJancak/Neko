<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\JobController as JobController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorklodController;

Route::middleware('auth:sanctum')->group(function () {
  Route::apiResource('jobs', JobController::class);
  Route::apiResource('users', UserController::class);
  Route::apiResource('worklods', WorklodController::class);
  Route::get('users/{user}/worklods', [UserController::class, 'worklods'])->name('users.worklods.index');
  Route::get('users/{user}/worklods/{date}', [UserController::class, 'getWorklodByDate'])->name('users.worklods.showByDate');
  Route::post('users/{user}/worklods', [UserController::class, 'assignWorklod'])->name('users.worklods.store');
  Route::delete('users/{user}/worklods/{worklod}', [UserController::class, 'removeWorklod'])->name('users.worklods.destroy');
});
