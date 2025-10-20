<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Autenticación
 *
 * Endpoints para el manejo de autenticación de usuarios.
 * Permite iniciar y cerrar sesión en el sistema.
 */
class LoginController extends Controller
{
    /**
     * Iniciar sesión
     *
     * Autentica a un usuario en el sistema y genera un token de acceso.
     *
     * @bodyParam email string requerido Email del usuario. Ejemplo: usuario@ejemplo.com
     * @bodyParam password string requerido Contraseña del usuario. Ejemplo: password123
     *
     * @response 200 {
     *   "success": true,
     *   "user": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "usuario@ejemplo.com"
     *   },
     *   "token": "1|abcdef123456",
     *   "message": "Login exitoso"
     * }
     *
     * @response 422 {
     *   "message": "Las credenciales proporcionadas son incorrectas.",
     *   "errors": {
     *     "email": ["Las credenciales proporcionadas son incorrectas."]
     *   }
     * }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token,
                'message' => 'Login exitoso'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas son incorrectas.'],
        ]);
    }

    /**
     * Cerrar sesión
     *
     * Revoca el token de acceso actual del usuario.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logout exitoso"
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ]);
    }
}