<?php

namespace App\Http\Controllers\Api\Login;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class LoginController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Administrador::where('email', $request->email)->first();

        // Si el correo no existe
        if (!$user) {
            return response()->json([
                'success' => false,
                'status' => 'EMAIL_NOT_FOUND',
                'message' => 'El correo electrónico no está registrado.',
            ], 401);
        }

        // Si la contraseña es incorrecta
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'status' => 'INVALID_PASSWORD',
                'message' => 'La contraseña es incorrecta.',
            ], 401);
        }

        // Login exitoso - continúa con el token
        $deviceName = $request->device_name ?? 'default';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso.',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Métod protegido (requiere autenticación)
    public function logout(Request $request)
    {
        // Eliminar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => 1,
            'message' => 'Sesión cerrada correctamente.'
        ]);
    }

}
