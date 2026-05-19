<?php

use App\Http\Controllers\Api\V1\AdoptionController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PetController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('pets', PetController::class);
        Route::apiResource('adoptions', AdoptionController::class);
    });
});
