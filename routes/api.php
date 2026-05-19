<?php

use App\Http\Controllers\Api\V1\AdoptionController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PetController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::apiResource('pets', PetController::class)->only(['index', 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('pets', PetController::class)
            ->except(['index', 'show'])
            ->middlewareFor(['store', 'update'], 'role:admin|staff')
            ->middlewareFor('destroy', 'role:admin');

        Route::apiResource('adoptions', AdoptionController::class)
            ->middlewareFor('store', 'role:adopter')
            ->middlewareFor(['index', 'show', 'update', 'destroy'], 'role:admin|staff');
    });
});
