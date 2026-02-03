<?php

use App\Http\Controllers\Api\Login\LoginController;
use App\Http\Controllers\Api\Auth\DashboardController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [LoginController::class, 'login']);



// Rutas protegidas (requieren token)
Route::middleware('auth:sanctum')->group(function () {
    // Información del usuario autenticado
    Route::get('/datos', [DashboardController::class, 'datos']);

    // Cerrar sesión
    Route::post('/logout', [LoginController::class, 'logout']);


});
