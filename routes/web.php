<?php
// routes/web.php — COMPLETE FINAL VERSION

use Illuminate\Support\Facades\Route;

// Public
Route::get('/',fn() => view('pages.home'));
Route::get('/login',fn() => view('pages.login'));
Route::get('/register',fn() => view('pages.register'));
Route::get('/properties/{id}',fn($id) => view('pages.property-detail', ['propertyId' => $id]));

// Auth required
Route::get('/profile',fn() => view('pages.profile'));
Route::get('/favorites',fn() => view('pages.favorites'));
Route::get('/reservations',fn() => view('pages.reservations'));
Route::get('/messages',fn() => view('pages.messages'));
Route::get('/messages/{id}',fn($id) => view('pages.conversation', ['convId' => $id]));

// Owner
Route::get('/my-properties',fn() => view('pages.my-properties'));
Route::get('/properties/create',fn() => view('pages.property-form'));
Route::get('/properties/{id}/edit',fn($id) => view('pages.property-form', ['propertyId' => $id]));
Route::get('/properties/{id}/stats',fn($id) => view('pages.property-stats'));
Route::get('/requests',fn() => view('pages.requests'));