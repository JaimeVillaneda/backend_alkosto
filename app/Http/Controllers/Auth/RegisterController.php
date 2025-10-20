<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

/**
 * @group Registro de Usuarios
 *
 * Endpoints para el registro y verificación de cuentas de usuario.
 * Permite crear nuevas cuentas y verificar las existentes.
 */
class RegisterController extends Controller
{
    /**
     * Registrar usuario
     *
     * Crea una nueva cuenta de usuario en el sistema.
     *
     * @bodyParam name string requerido Nombre completo del usuario. Ejemplo: "Juan Pérez"
     * @bodyParam email string requerido Email único del usuario. Ejemplo: "usuario@ejemplo.com"
     * @bodyParam password string requerido Contraseña (mínimo 8 caracteres). Ejemplo: "password123"
     * @bodyParam password_confirmation string requerido Confirmación de la contraseña. Ejemplo: "password123"
     *
     * @response 201 {
     *   "success": true,
     *   "user": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "usuario@ejemplo.com",
     *     "updated_at": "2023-01-01T12:00:00.000000Z",
     *     "created_at": "2023-01-01T12:00:00.000000Z"
     *   },
     *   "token": "1|abcdef123456",
     *   "message": "Usuario registrado exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "El email ya ha sido tomado.",
     *   "errors": {
     *     "email": ["El email ya ha sido tomado."]
     *   }
     * }
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'Usuario registrado exitosamente'
        ], 201);
    }

    /**
     * Verificar cuenta
     *
     * Endpoint para la verificación de cuentas de usuario.
     * Nota: Esta funcionalidad puede ser implementada según los requisitos específicos.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Cuenta verificada exitosamente"
     * }
     */
    public function verifyAccount(Request $request): JsonResponse
    {
        // Aquí puedes implementar la verificación de cuenta si es necesario
        return response()->json([
            'success' => true,
            'message' => 'Cuenta verificada exitosamente'
        ]);
    }
}