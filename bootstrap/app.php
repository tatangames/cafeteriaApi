<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Redirige guests a respuesta JSON en lugar de ruta web
        $middleware->redirectGuestsTo(fn () => abort(401, 'Usuario no autenticado'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo de autenticación
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'success' => 0,
                'message' => 'Usuario no autenticado.'
            ], 401);
        });

        // Manejo personalizado de excepciones de validación
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'success' => 0,
                'message' => 'Errores de validación.',
                'errors' => $e->errors()
            ], 422);
        });

        // Modelo no encontrado
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            return response()->json([
                'success' => 0,
                'message' => 'Recurso no encontrado.'
            ], 404);
        });

        // Métodos no permitido
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, Request $request) {
            return response()->json([
                'success' => 0,
                'message' => 'Método no permitido.'
            ], 405);
        });

        // Ruta no encontrada
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            return response()->json([
                'success' => 0,
                'message' => 'Ruta no encontrada.'
            ], 404);
        });

        // Error del servidor (opcional - descomenta si lo necesitas)
        $exceptions->render(function (\Throwable $e, Request $request) {
            // Solo para errores no manejados arriba
            if (!($e instanceof ValidationException) &&
                !($e instanceof AuthenticationException) &&
                !($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) &&
                !($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException)) {

                return response()->json([
                    'success' => 0,
                    'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor.',
                    'error' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ] : null
                ], 500);
            }
        });
    })->create();
