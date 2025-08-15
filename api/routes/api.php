<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\LPSPcreds;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductionController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

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
Route::post('/clients', [ClientController::class, 'createClient'])
    ->middleware('auth:sanctum')
    ->name('clients.create');
Route::delete('/clients/{id}', [ClientController::class, 'deleteClient'])
    ->middleware('auth:sanctum')
    ->name('clients.delete');
Route::patch('/clients', [ClientController::class, 'updateClient'])
    ->middleware('auth:sanctum')
    ->name('clients.update');

Route::get('/production', [ProductionController::class, 'getProductionData'])
    ->middleware('auth:sanctum')
    ->name('production.material.get');

Route::post('/material-create', [ProductionController::class, 'createMaterial'])
    ->middleware('auth:sanctum')
    ->name('production.material.create');
Route::patch('/material-update', [ProductionController::class, 'updateMaterial'])
    ->middleware('auth:sanctum')
    ->name('production.material.update');
Route::delete('/material/{id}', [ProductionController::class, 'deleteMaterial'])
    ->middleware('auth:sanctum')
    ->name('production.material.delete');

Route::delete('/product/{id}', [ProductionController::class, 'deleteProduct'])
    ->middleware('auth:sanctum')
    ->name('production.product.delete');

Route::post('/product-create', [ProductionController::class, 'createProduct'])
    ->middleware('auth:sanctum')
    ->name('production.product.create');

Route::patch('/product-update', [ProductionController::class, 'updateProduct'])
    ->middleware('auth:sanctum')
    ->name('production.product.update');


Route::middleware('auth:sanctum')->get('/download/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        return response()->json(['error' => 'File not found'], 404);
    }

    return response()->download($fullPath);
})->where('path', '.*');