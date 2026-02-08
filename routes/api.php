<?php

use App\Http\Controllers\Api\Login\LoginApiController;

use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\Auth\DashboardController;
use App\Http\Controllers\Api\Roles\RolesController;


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



    // ROLES Y PERMISOS
    Route::get('/admin/roles/tabla', [RolesController::class,'listadoRoles']);
    Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);
    Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
    Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
    Route::post('/admin/roles/nuevo-rol', [RolesController::class, 'nuevoRol']);
    Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
    Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
    Route::post('/admin/permisos/extra-borrar', [RolesController::class, 'borrarPermisoGlobal']);
    Route::post('/admin/permisos/extra-nuevo', [RolesController::class, 'nuevoPermisoExtra']);
    Route::get('/admin/usuarios/tabla', [RolesController::class,'tablaUsuarios']);
    Route::post('/admin/permisos/nuevo-usuario', [RolesController::class, 'nuevoUsuario']);
    Route::post('/admin/informacion/administrador', [RolesController::class, 'informacionAdministrador']);
    Route::put('/admin/actualizar/administrador/{id}', [RolesController::class, 'actualizarAdministrador']);




});
