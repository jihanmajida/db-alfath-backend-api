<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\GrupController;
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
});
