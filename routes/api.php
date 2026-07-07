<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ColourController;
use App\Http\Controllers\Api\CourierController;
use App\Http\Controllers\Api\CourierWorkloadController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkloadController;
use App\Http\Controllers\Api\UserDayStatusController;
use App\Http\Controllers\Api\UserStatusController;

Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('jobs', JobController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('colours', ColourController::class)->names('api.colours');
    
    // Workload Routes
    Route::get('workloads/calendar', [WorkloadController::class, 'calendar']);
    Route::apiResource('workloads', WorkloadController::class);
    Route::patch('workloads/{workload}/bike', [WorkloadController::class, 'assignBike']);

    // Specific User Workload associations
    Route::prefix('users/{user}')->group(function () {
        Route::get('workloads', [UserController::class, 'workloads']);
        Route::get('workloads/{date}', [UserController::class, 'getWorkloadByDate']);
        Route::post('workloads', [UserController::class, 'assignWorkload']);
        Route::delete('workloads/{workload}', [UserController::class, 'removeWorkload']);
    });
    Route::apiResource('user-day-statuses', UserDayStatusController::class);
    Route::apiResource('user-statuses', UserStatusController::class)->parameters([
        'user-statuses' => 'user_status' // Forces the parameter to be {user_status}
    ]);

    // Courier
    Route::get('courier/today-jobs', [CourierController::class, 'todayJobs']);
    Route::get('courier/jobs/{date}', [CourierController::class, 'jobsByDate']);
    Route::post('courier/tasks/{task}/status', [CourierController::class, 'updateTaskStatus']);
    Route::get('couriers/with-workload', CourierWorkloadController::class);
});
