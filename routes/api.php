<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DetailLaundryController;
use App\Http\Controllers\Api\GrupController;
use App\Http\Controllers\API\PelangganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    // Grup routes    
    Route::get('/grup', [GrupController::class, 'index']);
    Route::post('/grup', [GrupController::class, 'store']);
    Route::get('/grup/{id}', [GrupController::class, 'show']);
    Route::put('/grup/{id}', [GrupController::class, 'update']);
    Route::delete('/grup/{id}', [GrupController::class, 'destroy']);

    // Pelanggan routes
    Route::get('/pelanggan', [PelangganController::class, 'index']);
    Route::post('/pelanggan', [PelangganController::class, 'store']);
    Route::get('/pelanggan/{id}', [PelangganController::class, 'show']);
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update']);
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy']);

    // Detail Laundry routes
    Route::get('/detail', [DetailLaundryController::class, 'index']);
    Route::post('/detail', [DetailLaundryController::class, 'store']);
    Route::get('/detail/{id}', [DetailLaundryController::class, 'show']);
    Route::put('/detail/{id}', [DetailLaundryController::class, 'update']);
    Route::delete('/detail/{id}', [DetailLaundryController::class, 'destroy']);
});
