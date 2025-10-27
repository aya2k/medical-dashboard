<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WorkshopController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\BlogController;

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    

    Route::apiResource('/workshops', WorkshopController::class);

    Route::apiResource('services', ServiceController::class);

    Route::apiResource('blogs', BlogController::class);

    Route::apiResource('settings', SettingController::class);

    Route::apiResource('reviews', ReviewController::class);




 



    
});
