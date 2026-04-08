<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;

Route::prefix('auth')->group(function () {
    Route::post('/register',[AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
 
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::put('/auth/me', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

});

Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);
 
Route::middleware('auth:sanctum')->group(function () {
 
    Route::middleware('role:owner')->group(function () {
        Route::post('/properties', [PropertyController::class, 'store']);
 
        Route::put('/properties/{property}', [PropertyController::class, 'update']);
 
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);
 
        Route::get('/my-properties', [PropertyController::class, 'myProperties']);
 
        Route::patch('/properties/{property}/rent', [PropertyController::class, 'markAsRented']);
 
        Route::get('/properties/{property}/stats', [PropertyController::class, 'stats']);
 
        Route::post('/properties/{property}/images', [PropertyController::class, 'uploadImages']);
    });
});