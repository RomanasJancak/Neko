<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\JobController as JobController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkloadController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('jobs', JobController::class);
    Route::apiResource('users', UserController::class);
    
    // Workload Routes
    Route::get('workloads/calendar', [WorkloadController::class, 'calendar']);
    Route::apiResource('workloads', WorkloadController::class);

    // Specific User Workload associations
    Route::prefix('users/{user}')->group(function () {
        Route::get('workloads', [UserController::class, 'workloads']);
        Route::get('workloads/{date}', [UserController::class, 'getWorkloadByDate']);
        Route::post('workloads', [UserController::class, 'assignWorkload']);
        Route::delete('workloads/{workload}', [UserController::class, 'removeWorkload']);
    });
});
