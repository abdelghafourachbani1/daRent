<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MessagingController;


Route::prefix('auth')->group(function () {
    Route::post('/register',[AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);
 
Route::middleware('auth:sanctum')->group(function () {
 
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/me', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
 
    Route::middleware('role:owner')->group(function () {
        Route::post('/properties', [PropertyController::class, 'store']);
        Route::put('/properties/{property}', [PropertyController::class, 'update']);
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);
        Route::get('/my-properties', [PropertyController::class, 'myProperties']);
        Route::patch('/properties/{property}/rent', [PropertyController::class, 'markAsRented']);
        Route::get('/properties/{property}/stats', [PropertyController::class, 'stats']);
        Route::post('/properties/{property}/images', [PropertyController::class, 'uploadImages']);
        Route::post('/properties/{property}/images', [PropertyController::class, 'uploadImages']);
    });

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{propertyId}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{propertyId}', [FavoriteController::class, 'destroy']);
    Route::delete('/favorites', [FavoriteController::class, 'clearAll']);

    Route::middleware('role:tenant')->group(function () {
        Route::post('/properties/{propertyId}/reviews', [ReviewController::class, 'store']);
    });
    Route::get('/properties/{propertyId}/reviews', [ReviewController::class, 'index']);

    Route::post('/conversations', [MessagingController::class, 'createConversation']);
    Route::get('/conversations', [MessagingController::class, 'getConversations']); 
    Route::get('/conversations/{conversation}', [MessagingController::class, 'getConversation']); 
    Route::post('/messages', [MessagingController::class, 'sendMessage']); 
    Route::get('/messages/{conversationId}', [MessagingController::class, 'getMessages']);
    Route::delete('/messages/{message}', [MessagingController::class, 'deleteMessage']);
});