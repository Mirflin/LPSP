<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\LPSPcreds;
use App\Http\Controllers\ClientController;

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

Route::get('/credentials', [LPSPcreds::class, 'getCredentials'])
    ->middleware('auth:sanctum')
    ->name('credentials.get');
Route::patch('/credentials', [LPSPcreds::class, 'updateCredentials'])
    ->middleware('auth:sanctum')
    ->name('credentials.update');

Route::get('/clients', [ClientController::class, 'getClients'])
    ->middleware('auth:sanctum')
    ->name('clients.get');
Route::post('/clients', [ClientController::class, 'getClientsWithParams'])
    ->middleware('auth:sanctum')
    ->name('clients.get.params');