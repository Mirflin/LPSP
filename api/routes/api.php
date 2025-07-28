<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfilesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/profile', [ProfilesController::class, 'getProfile']);
Route::patch('/profile-update', [ProfilesController::class, 'updateProfile'])
    ->middleware('auth:sanctum')
    ->name('profile.update');
Route::patch('/profile-image', [ProfilesController::class, 'updateProfileImage'])
    ->middleware('auth:sanctum')
    ->name('profile.image.update');