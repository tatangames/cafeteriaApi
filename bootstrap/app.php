<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo personalizado de excepciones de validación para API
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Errores de validación.',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        // Manejo de autenticación
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => 0,
                    'message' => 'No autenticado.',
                ], 401);
            }
        });

        // Modelo no encontrado
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Recurso no encontrado.',
                ], 404);
            }
        });

        // Errores generales (opcional)
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') && !($e instanceof ValidationException) &&
                !($e instanceof \Illuminate\Auth\AuthenticationException) &&
                !($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException)) {

                return response()->json([
                    'success' => 0,
                    'message' => config('app.debug') ? $e->getMessage() : 'Error del servidor.',
                ], 500);
            }
        });
    })->create();
