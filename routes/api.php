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

  Route::get('/settings', [SettingController::class, 'index']);
  Route::post('/settings/save', [SettingController::class, 'save']);


  Route::apiResource('reviews', ReviewController::class);
});




Route::prefix('user')->group(function () {


  Route::get('workshops', [WorkshopController::class, 'index']);
  Route::get('workshops/{workshop}', [WorkshopController::class, 'show']);

  Route::get('services', [ServiceController::class, 'index']);
  Route::get('services/{service}', [ServiceController::class, 'show']);

  Route::get('blogs', [BlogController::class, 'index']);
  Route::get('blogs/{blog}', [BlogController::class, 'show']);

  Route::get('/settings', [SettingController::class, 'index']);

  Route::get('reviews', [ReviewController::class, 'index']);
  Route::get('reviews/{review}', [ReviewController::class, 'show']);
});
