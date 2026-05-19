<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::apiResource('pets', App\Http\Controllers\Api\V1\PetController::class);
        Route::apiResource('adoptions', App\Http\Controllers\Api\V1\AdoptionController::class);
    });
});
