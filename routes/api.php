<?php

use App\Http\Controllers\Api\Login\LoginController;
use App\Http\Controllers\Api\Auth\DashboardController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [LoginController::class, 'login']);

Route::post('/validate-reset-token', [LoginController::class, 'validateResetToken']);
Route::post('/reset-password-confirm', [LoginController::class, 'resetPasswordConfirm']);



Route::post('/admin/enviar/correo/password', [LoginController::class, 'enviarCorreoAdministrador']);


Route::get('/admin/resetear/contrasena/administrador/{token}', [LoginController::class,'indexIngresoNuevaPasswordLink']);
Route::post('/admin/administrador/actualizacion/password', [LoginController::class, 'actualizarPasswordAdministrador']);





// Rutas protegidas (requieren token)
Route::middleware('auth:sanctum')->group(function () {
    // Información del usuario autenticado
    Route::get('/datos', [DashboardController::class, 'datos']);

    // Cerrar sesión
    Route::post('/logout', [LoginController::class, 'logout']);


});
