<?php

use App\Http\Controllers\Api\Login\LoginApiController;

use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\Auth\DashboardController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [LoginApiController::class, 'login']);

Route::post('/validate-reset-token', [LoginApiController::class, 'validateResetToken']);
Route::post('/reset-password-confirm', [LoginApiController::class, 'resetPasswordConfirm']);
Route::post('/admin/enviar/correo/password', [LoginApiController::class, 'enviarCorreoAdministrador']);





// Rutas protegidas (requieren token)
Route::middleware('auth:sanctum')->group(function () {
    // Cerrar sesión
    Route::post('/logout', [LoginApiController::class, 'logout']);

    // Usuario autenticado + roles + permisos (CLAVE)
    Route::get('/me', [AuthApiController::class, 'me']);

    // Información del usuario autenticado
    Route::get('/datos', [DashboardController::class, 'datos']);

    Route::get('/admin/roles/tabla', [AuthApiController::class,'listadoRoles']);


});
